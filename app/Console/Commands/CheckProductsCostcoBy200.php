<?php
namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class CheckProductsCostcoBy200 extends CheckCostcoProducts
{
    protected $signature = 'costco:check_products {offset=0}';
    protected $description = 'Check products from site 1 for changes in price or stock';

    public function getProducts()
    {
        $offset = $this->argument('offset');
        $products = Product::where('site', 1)->skip($offset)->take(200)->get();
        return $products;
    }
}
