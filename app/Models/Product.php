<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'image',
        'price',
        'stock',
        'url',
        'isScand',
        'site',
        'stock_level',
        'discount_exp',
        'discount_value',
        'value_price',
        'existance',
        'code'
    ];

    /**
     * Get the user associated with the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */

    /**
     * Get all of the comments for the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function warnings()
    {
        return $this->hasMany('App\Models\Warning', 'product_id');
    }
}
