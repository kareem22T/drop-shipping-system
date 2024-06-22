<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Warning;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use App\Traits\SendEmailTrait;

abstract class CheckCostcoProducts extends Command
{
    use SendEmailTrait;

    protected $proxies = [];

    public function handle()
    {
        // Log::info($this->description . ' started.');

        $products = $this->getProducts();
        foreach ($products as $product) {
            $this->checkProductChanges($product);
            sleep(2);  // Sleep for 2 seconds between requests
        }

        // Log::info($this->description . ' completed.');
    }

    abstract protected function getProducts();

    protected function fetchProduct($url)
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

    protected function checkProductChanges($product)
    {
        $fetched_product = $this->fetchProduct($product->url);

        if (!$fetched_product) {
            Log::error("Failed to refetch product: {$product->id}");
            return;
        }

        $newPrice = isset($fetched_product->basePrice) ? $fetched_product->basePrice->formattedValue : "N/A";

        $newStock = $fetched_product->stock->stockLevel > 0 ? 1 : 0;
        $newStockLevel = $fetched_product->stock->stockLevel;

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

        if ($product->stock_level != $newStockLevel) {
            $product->update([
                'stock_level' => $newStockLevel,
            ]);
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
                $content .= "<br>";
                $content .= "<a href='" . $product->url . "'>";
                $content .= "Link Here";
                $content .= "</a>";

                $this->sendEmail("Mohamed.attia1234@outlook.com", "Warning", $content);
            }
            // Optionally update the product in the database
            $product->update([
                'price' => $newPrice,
                'stock' => $newStock
            ]);
        }
    }

    protected function fetchProxiesFromApi()
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

    protected function getRandomProxy()
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
