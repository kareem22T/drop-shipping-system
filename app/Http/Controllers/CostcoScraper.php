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

        $result = isset(explode('/p/', $url)[1]) ? explode('/p/', $url)[1] : null;

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
            'url' => 'required|array|max:10',
            'url.*' => 'required|url|regex:/https:\/\/www\.costco\.co\.uk\/.+\/p\/.+/',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        // Call the static method with self::
        foreach ($request->url as $item) {
            $fetched_product = self::fetchProduct($item);

            if (!$fetched_product)
                return redirect()->back()->withErrors(["general" => "Something wrong happened"]);

            $name = $fetched_product->englishName;
            $image = "https://www.costco.co.uk" . $fetched_product->images[0]->url;

            // Assuming $fetched_product->images is an array of image objects
            if (!empty($fetched_product->images)) {
                foreach ($fetched_product->images as $imageObj) {
                    if (isset($imageObj->url) && in_array(strtolower(pathinfo($imageObj->url, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'])) {
                        $image = "https://www.costco.co.uk" . $imageObj->url;
                        break;
                    }
                }
            }
            $price = isset($fetched_product->basePrice) ? $fetched_product->basePrice->formattedValue : "N/A";
            $value_price = isset($fetched_product->basePrice) ? $fetched_product->basePrice->value : "N/A";
            $stock = $fetched_product->stock->stockLevel > 0 ? 1 : 0;
            $stock_level = $fetched_product->stock->stockLevel ;
            $discount = isset($fetched_product->couponDiscount) ? $fetched_product->couponDiscount->discountValue : 0;
            $discount_exp = isset($fetched_product->couponDiscount) ? $fetched_product->couponDiscount->discountEndDate : null;
            $url = $item;
            $code = getStringAfterP2($item);
            $product_exists = Product::where("code", $code)->first();
            if ($product_exists)
                return redirect()->back()->withErrors(["general" => "Product already exists"]);

            $product = Product::create([
                "name" => $name,
                "image" => $image,
                "price" => $price,
                "value_price" => $value_price,
                "discount_value" => $discount,
                "discount_exp" => $discount_exp,
                "stock" => $stock,
                "site" => 1,
                "url" => $url,
                "code" => $code,
                "stock_level" => $stock_level,
            ]);
        }

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
            // Log::error('Error fetching proxies: ' . $e->getMessage());
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
