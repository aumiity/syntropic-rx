<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    protected $table = 'sales';

    protected $fillable = [
        'invoice_no',
        'sale_type',
        'customer_id',
        'customer_name_free',
        'sold_by',
        'sold_at',
        'age_range',
        'symptom_note',
        'subtotal',
        'total_discount',
        'total_vat',
        'total_amount',
        'cash_amount',
        'card_amount',
        'transfer_amount',
        'change_amount',
        'is_credit',
        'due_date',
        'is_fda13_report',
        'sale_report_note',
        'status',
        'void_reason',
        'note',
    ];

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(\App\Models\Customer::class);
    }
}
