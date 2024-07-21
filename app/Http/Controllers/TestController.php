<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function testSend()
    {
        $url = 'https://www.amazon.co.uk/Rolson-Quality-Tools-Ltd-61702/dp/B003KGB992/?th=1'; // replace with your Amazon product URL

        // Create an HTTP client
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
        return response()->json([
            'quantity' => $quantityText,
            'price' => $priceText,
            'ImageText' => $ImageText,
            'titleText' => $titleText,
        ]);
    }
}
