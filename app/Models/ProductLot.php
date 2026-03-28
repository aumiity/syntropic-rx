<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductLot extends Model
{
    protected $fillable = [
        'product_id', 'supplier_id', 'lot_number',
        'manufactured_date', 'expiry_date',
        'cost_price', 'sell_price',
        'qty_received', 'qty_on_hand', 'qty_reserved',
        'is_closed',
    ];

    protected $casts = [
        'expiry_date'      => 'date',
        'manufactured_date'=> 'date',
        'is_closed'        => 'boolean',
        'cost_price'       => 'decimal:2',
        'sell_price'       => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // เช็คว่าใกล้หมดอายุไหม
    public function getIsExpiringSoonAttribute()
    {
        return $this->expiry_date->diffInDays(now()) <= 90;
    }
}
