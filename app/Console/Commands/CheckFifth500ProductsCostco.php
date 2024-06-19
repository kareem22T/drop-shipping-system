<?php

namespace App\Console\Commands;

use App\Models\Product;

class CheckFifth500ProductsCostco extends CheckCostcoProducts
{
    protected $signature = 'costco:check_fifth_500';
    protected $description = 'Check first 500 products from site 1 for changes in price or stock';

    protected function getProducts()
    {
        return Product::where('site', 1)->skip(2000)->take(500)->get();
    }
}
