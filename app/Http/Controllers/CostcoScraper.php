<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;

class CostcoScraper extends Controller
{
    private $proxies = [];

    private static function fetchProduct($url)
    {
        // Define the helper function inside the method
        function getStringAfterP($url) {
            $parts = explode('/p/', $url);
            return isset($parts[1]) ? $parts[1] : null;
        }

        $result = getStringAfterP($url);

        // Assume getRandomProxy is another static method or remove it if not needed
        // $proxy = self::getRandomProxy();

        $client = new Client();
        $url = 'https://www.costco.co.uk/rest/v2/uk/products/' . $result . '/?fields=FULL&lang=en_GB&curr=GBP';

        $options = [];

        try {
            $response = $client->request('GET', $url, $options);
            $body = json_decode($response->getBody());
            return $body;
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    public static function insertProduct($request) {
        function getStringAfterP2($url) {
            $parts = explode('/p/', $url);
            return isset($parts[1]) ? $parts[1] : null;
        }

        $validator = Validator::make($request->all(), [
            'url' => 'required|url|regex:/https:\/\/www\.costco\.co\.uk\/.+\/p\/.+/',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        // Call the static method with self::
        $fetched_product = self::fetchProduct($request->url);

        if (!$fetched_product)
            return redirect()->back()->withErrors(["general" => "Something wrong happened"]);

        $name = $fetched_product->englishName;
        $image = "https://www.costco.co.uk" . $fetched_product->images[0]->url;
        function isImageURL($url) {
            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
            $ext = pathinfo($url, PATHINFO_EXTENSION);
            return in_array(strtolower($ext), $imageExtensions);
        }

        // Assuming $fetched_product->images is an array of image objects
        if (!empty($fetched_product->images)) {
            foreach ($fetched_product->images as $imageObj) {
                if (isset($imageObj->url) && isImageURL($imageObj->url)) {
                    $image = "https://www.costco.co.uk" . $imageObj->url;
                    break;
                }
            }
        }
        $price = $fetched_product->basePrice->formattedValue;
        $stock = $fetched_product->stock->stockLevel > 0 ? 1 : 0;
        $stock_level = $fetched_product->stock->stockLevel ;
        $url = $request->url;
        $code = getStringAfterP2($request->url);
        $product_exists = Product::where("code", $code)->first();
        if ($product_exists)
            return redirect()->back()->withErrors(["general" => "Product already exists"]);

        $product = Product::create([
            "name" => $name,
            "image" => $image,
            "price" => $price,
            "stock" => $stock,
            "site" => 1,
            "url" => $url,
            "code" => $code,
            "stock_level" => $stock_level,
        ]);

        return redirect()->to('/')
        ->with('success', 'Product added successfuly');

    }
    private function fetchProxiesFromApi()
    {
        $client = new Client();
        $apiUrl = 'https://proxylist.geonode.com/api/proxy-list?limit=500&page=1&sort_by=lastChecked&sort_type=desc'; // Replace with your actual API endpoint

        try {
            $response = $client->request('GET', $apiUrl);
            $res = json_decode($response->getBody());
            if ($res)
                $this->proxies = $res->data;
        } catch (\Exception $e) {
            // Handle the exception if needed
            // For now, let's just log the error
            \Log::error('Error fetching proxies: ' . $e->getMessage());
        }
    }

    private function getRandomProxy()
    {
        // Fetch proxies if not already fetched
        if (empty($this->proxies)) {
            $this->fetchProxiesFromApi();
        }

        if (!empty($this->proxies)) {
            return $this->proxies[array_rand($this->proxies)];
        }

        // Return null or a default proxy if no proxies are available
        return null;
    }
}
