<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductUnit extends Model
{

    protected $fillable = [
        'product_id',
        'unit_name',
        'barcode',
        'qty_per_base',
        'price_retail',
        'price_wholesale1',
        'price_wholesale2',
        'is_for_sale',
        'is_for_purchase',
        'is_disabled',
    ];


    protected $casts = [
        'qty_per_base'     => 'decimal:4',
        'price_retail'     => 'decimal:2',
        'price_wholesale1' => 'decimal:2',
        'price_wholesale2' => 'decimal:2',
        'is_for_sale'      => 'boolean',
        'is_for_purchase'  => 'boolean',
        'is_disabled'      => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }


}
