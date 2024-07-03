<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warning extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'change',
        'old',
        'hide',
        'new'
    ];

    public function product()
    {
        return $this->hasOne(Product::class, "id", 'product_id');
    }
}
