<?php

namespace App\Console\Commands;

use App\Models\Product;

class CheckFourth500ProductsCostco extends CheckCostcoProducts
{
    protected $signature = 'costco:check_fourth_500';
    protected $description = 'Check first 500 products from site 1 for changes in price or stock';

    protected function getProducts()
    {
        return Product::where('site', 1)->skip(1500)->take(500)->get();
    }
}
