<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Warning;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use App\Traits\SendEmailTrait;

class CheckCostcoProducts extends Command
{
    use SendEmailTrait;
    protected $signature = 'costco:check';
    protected $description = 'Refetch products from site 1 and check for changes in price or stock';

    private $proxies = [];

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        Log::info('RefetchProducts command started.');

        $products = Product::where('site', 1)->take(50)->get();
        foreach ($products as $product) {
            $this->checkProductChanges($product);
            sleep(2);
        }

        Log::info('RefetchProducts command completed.');
    }

    private function fetchProduct($url)
    {
        $result = $this->getStringAfterP($url);
        $client = new Client();
        $url = 'https://www.costco.co.uk/rest/v2/uk/products/' . $result . '/?fields=FULL&lang=en_GB&curr=GBP';

        $options = [];

        try {
            $response = $client->request('GET', $url, $options);
            return json_decode($response->getBody());
        } catch (\Exception $e) {
            Log::error('Error fetching product: ' . $e->getMessage());
            return null;
        }
    }

    private function getStringAfterP($url)
    {
        $parts = explode('/p/', $url);
        return isset($parts[1]) ? $parts[1] : null;
    }

    private function checkProductChanges($product)
    {
        $fetched_product = $this->fetchProduct($product->url);

        if (!$fetched_product) {
            Log::error("Failed to refetch product: {$product->id}");
            return;
        }

        $newPrice = $fetched_product->basePrice->formattedValue;
        $newStock = $fetched_product->stock->stockLevel > 0 ? ($fetched_product->stock->stockLevel > 10 ? 1 : 2) : 0;

        $changes = [];

        if ($product->price != $newPrice) {
            $changes['price'] = [
                'old' => $product->price,
                'new' => $newPrice
            ];
        }

        if ($product->stock != $newStock) {
            $changes['stock'] = [
                'old' => $product->stock,
                'new' => $newStock
            ];
        }

        if (!empty($changes)) {
            Log::info("Product {$product->id} has changes:", $changes);
            foreach ($changes as $key => $change) {
                Warning::create([
                    "product_id" => $product->id,
                    "change" => $key,
                    "old" => $change['old'],
                    "new" => $change['new'],
                ]);

                $content = "Product " . "<b>" . $product->name . "</b>" . " " . $key . " has changed from ";
                $content .= "<b>";
                $content .= $key == "stock" ? ($change['old'] == 1 ? "In Stock" : ($change['old'] == 2 ? "Managed Stock" : "Out Of Stock")) : $change['old'];
                $content .= "</b>";
                $content .= " to ";
                $content .= "<b>";
                $content .= $key == "stock" ? ($change['new'] == 1 ? "In Stock" : ($change['new'] == 2 ? "Managed Stock" : "Out Of Stock")) : $change['new'];
                $content .= "</b>";

                $this->sendEmail("kotbekareem74@gmail.com", "Warning", $content);
            }
            // Optionally update the product in the database
            $product->update([
                'price' => $newPrice,
                'stock' => $newStock
            ]);
        }
    }

    private function fetchProxiesFromApi()
    {
        $client = new Client();
        $apiUrl = 'https://proxylist.geonode.com/api/proxy-list?limit=500&page=1&sort_by=lastChecked&sort_type=desc';

        try {
            $response = $client->request('GET', $apiUrl);
            $res = json_decode($response->getBody());
            if ($res)
                $this->proxies = $res->data;
        } catch (\Exception $e) {
            Log::error('Error fetching proxies: ' . $e->getMessage());
        }
    }

    private function getRandomProxy()
    {
        if (empty($this->proxies)) {
            $this->fetchProxiesFromApi();
        }

        if (!empty($this->proxies)) {
            return $this->proxies[array_rand($this->proxies)];
        }

        return null;
    }
}
