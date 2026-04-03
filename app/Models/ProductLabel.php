<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductLabel extends Model
{
    protected $table = 'product_labels';

    protected $fillable = [
        'product_id',
        'label_name',
        'dosage_id',
        'frequency_id',
        'timing_id',
        'meal_relation_id',
        'label_time_id',
        'advice_id',
        'indication_th',
        'indication_mm',
        'indication_zh',
        'note_th',
        'note_mm',
        'note_zh',
        'show_barcode',
        'is_default',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'show_barcode' => 'boolean',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];
}
