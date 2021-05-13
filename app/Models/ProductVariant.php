<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'variant', 'variant_id', 'product_id'
    ];

    function variants() {
      return $this->belongsTo('App\Models\Variant');
    }
}
