<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class CheckAmazonFirst250Products extends CheckAmazonProducts
{
    protected $signature = 'amazon:check_first_250';
    protected $description = 'Check first 250 products from site 2 for changes in price or stock';

    protected function getProducts()
    {
        return Product::where('site', 2)->skip(0)->take(250)->get();
    }
}
