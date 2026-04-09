<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItemLot extends Model
{
    protected $table = 'sale_item_lots';

    public $timestamps = false;

    protected $fillable = [
        'sale_item_id',
        'lot_id',
        'product_id',
        'qty',
        'is_cancelled',
    ];

    public function saleItem(): BelongsTo
    {
        return $this->belongsTo(SaleItem::class);
    }

    public function lot()
    {
        return $this->belongsTo(\App\Models\ProductLot::class, 'lot_id');
    }
}
