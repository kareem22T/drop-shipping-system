<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Warning;
use Illuminate\Support\Facades\Log;
use App\Traits\SendEmailTrait;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DomCrawler\Crawler;

abstract class CheckAmazonProducts extends Command
{
    use SendEmailTrait;

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
        $client = HttpClient::create([
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            ]
        ]);
        // Make a request to fetch the HTML content
        $response = $client->request('GET', $url);
        $htmlContent = $response->getContent();

        // Initialize the Crawler with the HTML content
        $crawler = new Crawler($htmlContent);

        // Use the XPath you copied for quantity
        $quantityXPath = '//*[@id="availability"]/span'; // replace with your copied XPath
        $quantityElement = $crawler->filterXPath($quantityXPath);
        $quantityText = $quantityElement ? $quantityElement->text() : 'Quantity not found';

        // Use the XPath you copied for price
        $priceXPath = '//*[@id="priceValue"]'; // replace with your copied XPath
        $priceElement = $crawler->filterXPath($priceXPath);
        $priceText = $priceElement ? $priceElement->attr("value") : 'Price not found';

        $ImageXPath = '//*[@id="landingImage"]'; // replace with your copied XPath
        $ImageElement = $crawler->filterXPath($ImageXPath);
        $ImageText = $ImageElement ? $ImageElement->attr("src") : 'img not found';

        $titleXPath = '//*[@id="title"]'; // replace with your copied XPath
        $titleElement = $crawler->filterXPath($titleXPath);
        $titleText = $titleElement ? $titleElement->text() : '#title not found';

        // Return the scraped data as JSON
        return [
            'quantity' => $quantityText,
            'price' => $priceText,
            'image' => $ImageText,
            'title' => $titleText,
        ];
    }


    protected function checkProductChanges($product)
    {
        $fetchedData = $this->fetchProduct($product->url);
        $fetched_product = $fetchedData;

        if ($fetched_product) {

            $newPrice = $fetched_product['price'];

            $newStock = $fetched_product['quantity'];


            if ($product->price != $newPrice) {
                $changes['price'] = [
                    'old' => $product->price,
                    'new' => $newPrice
                ];
                $product->update([
                    'price' => $newPrice,
                ]);
            }

            if ($product->stock != $newStock) {
                $changes['stock'] = [
                    'old' => $product->stock,
                    'new' => $newStock
                ];
                $product->update([
                    'stock' => $newStock,
                ]);
            }


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
                $content .= $key ==  $change['old'];
                $content .= "</b>";
                $content .= " to ";
                $content .= "<b>";
                $content .=  $change['new'];
                $content .= "</b>";
                $content .= "<br>";
                $content .= "<a href='" . $product->url . "'>";
                $content .= "Link Here";
                $content .= "</a>";

                $this->sendEmail("Mohamed.attia1234@outlook.com", "Warning Amazon UK", $content);
            }

        }
    }
}
