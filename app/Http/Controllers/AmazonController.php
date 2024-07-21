<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;

class AmazonController extends Controller
{
    private static function fetchProduct($url)
    {
        $client = HttpClient::create([
        ]);
        // Make a request to fetch the HTML content
        $response = $client->request('GET', $url);
        $htmlContent = $response->getContent();

        // Initialize the Crawler with the HTML content
        $crawler = new Crawler($htmlContent);

        // Use the XPath you copied for quantity
        $quantityXPath = '//*[@id="availability"]/span'; // replace with your copied XPath
        $quantityElement = $crawler->filterXPath($quantityXPath);
        $quantityText = $quantityElement->count() > 0 ? $quantityElement->text() : 'Quantity not found';

        // Use the XPath you copied for price
        $priceXPath = '//*[@id="priceValue"]'; // replace with your copied XPath
        $priceElement = $crawler->filterXPath($priceXPath);
        $priceText = $priceElement->count() > 0 ? $priceElement->attr("value") : 'Price not found';

        $ImageXPath = '//*[@id="landingImage"]'; // replace with your copied XPath
        $ImageElement = $crawler->filterXPath($ImageXPath);
        $ImageText = $ImageElement->count() > 0 ? $ImageElement->attr("src") : 'img not found';

        $titleXPath = '//*[@id="title"]'; // replace with your copied XPath
        $titleElement = $crawler->filterXPath($titleXPath);
        $titleText = $titleElement->count() > 0 ? $titleElement->text() : '#title not found';

        // Return the scraped data as JSON
        return [
            'quantity' => $quantityText,
            'price' => $priceText,
            'image' => $ImageText,
            'title' => $titleText,
        ];
    }

    public static function insertProduct($request) {

        $validator = Validator::make($request->all(), [
            'url' => 'required|array|max:10',
            'url.*' => 'required|url',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        // Call the static method with self::
        foreach ($request->url as $item) {
            $fetched_product = self::fetchProduct($item);

            $price = $fetched_product['price'];
            $stock = $fetched_product['quantity'];
            $image = $fetched_product['image'];
            $title = $fetched_product['title'];
            $url = $item;

            $product = Product::create([
                "name" => $title,
                "image" => $image,
                "price" => $price,
                "value_price" => '0',
                "stock" => $stock,
                "site" => 2,
                "url" => $url,
                "code" => "Amazon",
                "stock_level" => 1,
            ]);
        }

        return redirect()->to('/amazon')
        ->with('success', 'Product added successfuly');

    }

}
