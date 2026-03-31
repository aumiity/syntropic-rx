<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductUnit extends Model
{
    protected $fillable = [
        'product_id',
        'unit_id',
        'barcode',
        'qty_per_base',
        'price_retail',
        'price_wholesale1',
        'is_for_sale',
        'is_for_purchase',
        'is_disabled',
    ];

    protected $casts = [
        'qty_per_base' => 'decimal:4',
        'price_retail' => 'decimal:2',
        'price_wholesale1' => 'decimal:2',
        'is_for_sale' => 'boolean',
        'is_for_purchase' => 'boolean',
        'is_disabled' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unit()
    {
        return $this->belongsTo(ItemUnit::class);
    }
}
