<?php

namespace App\Console\Commands;

use App\Models\Product;

class CheckSixth500ProductsCostco extends CheckCostcoProducts
{
    protected $signature = 'costco:check_sixth_500';
    protected $description = 'Check first 500 products from site 1 for changes in price or stock';

    protected function getProducts()
    {
        return Product::where('site', 1)->skip(2500)->take(500)->get();
    }
}
