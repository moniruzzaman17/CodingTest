<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariantPrice extends Model
{
    protected $fillable = [
        'product_variant_one', 'product_variant_two', 'product_variant_three', 'price', 'stock', 'product_id'
    ];

    function variantOne() {
      return $this->hasOne('App\Models\ProductVariant','id','product_variant_one');
    }

    function variantTwo() {
      return $this->hasOne('App\Models\ProductVariant','id','product_variant_two');
    }

    function variantThree() {
      return $this->hasOne('App\Models\ProductVariant','id','product_variant_three');
    }
}
