<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Warning;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use App\Traits\SendEmailTrait;
use DateTime;

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
            $statusCode = $response->getStatusCode();
            return ['product' => json_decode($response->getBody()), 'statusCode' => $statusCode];
        } catch (\Exception $e) {
            $statusCode = $e->getCode();
            // Log::error('Error fetching product: ' . $e->getMessage());
            return ['product' => null, 'statusCode' => $statusCode];
        }
    }

    private function getStringAfterP($url)
    {
        $parts = explode('/p/', $url);
        return isset($parts[1]) ? $parts[1] : null;
    }

    protected function checkProductChanges($product)
    {
        try {
            $fetchedData = $this->fetchProduct($product->url);
            $fetched_product = $fetchedData['product'];
            $statusCode = $fetchedData['statusCode'];
            $changes = [];


            if ($statusCode == 404) {
                // Log::info("Product {$product->id} does not exist.");
                $newExistance = 0;
                $oldExistance = $product->existance;
                if ($newExistance != $oldExistance) {
                    // Log::info("existance changes");
                    $changes['existance'] = [
                        'old' => $oldExistance,
                        'new' => $newExistance
                    ];
                }
                $product->update(['existance' => false]);
            } elseif ($statusCode == 200) {
                $newExistance = 1;
                $oldExistance = $product->existance;
                if ($newExistance != $oldExistance) {
                    // Log::info("existance changes");
                    $changes['existance'] = [
                        'old' => $oldExistance,
                        'new' => $newExistance
                    ];
                }
                $product->update(['existance' => true]);
            }

            if ($fetched_product) {

                $newPrice = isset($fetched_product->basePrice) ? $fetched_product->basePrice->formattedValue : "N/A";
                $value_price = isset($fetched_product->basePrice) ? $fetched_product->basePrice->value : "N/A";

                $newStock = (isset($fetched_product->stock->stockLevel) && $fetched_product->stock->stockLevel > 0) ? 1 : 0;
                $newStockLevel = isset($fetched_product->stock->stockLevel) ? $fetched_product->stock->stockLevel : 0;
                $discount = isset($fetched_product->couponDiscount) ? $fetched_product->couponDiscount->discountValue : 0;
                $discount_exp = isset($fetched_product->couponDiscount) ? $fetched_product->couponDiscount->discountEndDate : null;


                if ($product->price != $newPrice) {
                    $changes['price'] = [
                        'old' => $product->price,
                        'new' => $newPrice
                    ];
                    $product->update([
                        'price' => $newPrice,
                    ]);
                }

                if ((int) $product->stock != (int) $newStock) {
                    $changes['stock'] = [
                        'old' => $product->stock,
                        'new' => $newStock
                    ];
                    $product->update([
                        'stock' => $newStock,
                        'stock_level' => $newStockLevel,
                    ]);
                }

                if ($product->stock_level != $newStockLevel) {
                    $product->update([
                        'stock_level' => $newStockLevel,
                        'stock' => $newStock,
                    ]);
                }

                $tomorrow = new DateTime('tomorrow');
                $today = new DateTime();

                // Convert the product's discount expiration date to a DateTime object
                $product_discount_exp = new DateTime($product->discount_exp);

                // Check if the discount expiration date is tomorrow
                if ($product_discount_exp->format('Y-m-d') == $tomorrow->format('Y-m-d')) {
                    $changes['exp_warn'] = [
                        'old' => "N\N",
                        'new' => "Tomorrow"
                    ];
                }

                // Check if the discount expiration date has passed
                if ($product_discount_exp < $today) {
                    $changes['discount_value'] = [
                        'old' => $product->discount_value,
                        'new' => 0
                    ];
                    $product->update([
                        'discount_exp' => null,
                        'discount_value' => 0,
                    ]);
                } else {
                    if ((float) $product->discount_value != (float) $discount) {
                        if ($discount_exp) {
                            $changes['discount_value'] = [
                                'old' => $product->discount_value,
                                'new' => $discount
                            ];
                            $product->update([
                                'discount_value' => $discount,
                                'discount_exp' => $discount_exp,
                            ]);
                        }
                    }
                }

                if ($product->value_price != $value_price) {
                    $product->update([
                        'value_price' => $value_price,
                    ]);
                }
            }
            if (!empty($changes)) {
                // Log::info("Product {$product->id} has changes:", $changes);
                foreach ($changes as $key => $change) {
                    Warning::create([
                        "product_id" => $product->id,
                        "change" => $key,
                        "old" => $change['old'],
                        "new" => $change['new'],
                    ]);

                    if ($key === "exp_warn") {
                        $content = "Product " . "<b>" . $product->name . "</b>" . " discount is about to expired";
                        $content .= "<br>";
                        $content .= "<a href='" . $product->url . "'>";
                        $content .= "Link Here";
                        $content .= "</a>";

                        $this->sendEmail("Mohamed.attia1234@outlook.com", "Warning", $content);
                    } else {
                        if ($key == 'discount_value') {
                            $content = "Product " . "<b>" . $product->name . "</b>" . " " . "Discount Value" . " has changed from ";
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
                        } else if ($key == 'existance') {
                            $content = "Product " . "<b>" . $product->name . "</b>" . " " . "availability" . " has changed from ";
                            $content .= "<b>";
                            $content .= $key == "existance" ? ($change['old'] == 1 ? "Avilable" : "Not avilable") : $change['old'];
                            $content .= "</b>";
                            $content .= " to ";
                            $content .= "<b>";
                            $content .= $key == "existance" ? ($change['new'] == 1 ? "Avilable" : "Not avilable") : $change['new'];
                            $content .= "</b>";
                            $content .= "<br>";
                            $content .= "<a href='" . $product->url . "'>";
                            $content .= "Link Here";
                            $content .= "</a>";

                            $this->sendEmail("Mohamed.attia1234@outlook.com", "Warning", $content);
                        } else {
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
                    }
                }
            }

            Log::info("product: " . $product->id . ' is scand');
            // $product->isScand = true;
            // $product->save();
        } catch (\Exception $e) {
            Log::error("Error checking product {$product->id}: " . $e->getMessage());
            // Optionally: Add product to a retry list or queue
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
