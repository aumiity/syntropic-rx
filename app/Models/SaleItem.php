<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SaleItem extends Model
{
    protected $table = 'sale_items';

    public $timestamps = false;

    protected $fillable = [
        'sale_id',
        'product_id',
        'item_name',
        'unit_name',
        'qty',
        'unit_price',
        'discount',
        'unit_vat',
        'line_total',
        'item_note',
        'is_cancelled',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function saleItemLots(): HasMany
    {
        return $this->hasMany(SaleItemLot::class);
    }
}
