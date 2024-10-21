<?php
namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class CheckProductsCostcoByIsNotScanned extends CheckCostcoProducts
{
    protected $signature = 'costco:check_products_not_scaned {offset=0}';
    protected $description = 'Check products from site 1 for changes in price or stock';

    public function getProducts()
    {
        $offset = $this->argument('offset');
        $products = Product::where('site', 1)->where("isScand", false)->skip($offset)->take(50)->get();
        return $products;
    }
}
