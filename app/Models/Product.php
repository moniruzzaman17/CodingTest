<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'title', 'sku', 'description'
    ];

    function productVariant() {
      return $this->hasMany('App\Models\ProductVariant','product_id','id');
    }

    function images() {
      return $this->hasMany('App\Models\ProductImage','product_id','id');
    }

    function variantPrice() {
      return $this->hasMany('App\Models\ProductVariantPrice','product_id','id');
    }

    function price() {
      return $this->hasOne('App\Models\ProductVariantPrice','product_id','id');
    }

}
