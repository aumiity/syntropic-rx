<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function productUnits()
    {
        return $this->hasMany(\App\Models\ProductUnit::class);
    }
    protected $fillable = [
        'barcode', 'barcode2', 'code', 'trade_name', 'name_for_print', 'search_keywords',
        'category_id', 'dosage_form_id', 'default_qty', 'unit_name',
        'drug_generic_name_id',
        'price_retail', 'price_wholesale1', 'price_wholesale2',
        'is_vat', 'is_not_discount',
        'reorder_point', 'safety_stock',
        'expiry_alert_days1', 'expiry_alert_days2', 'expiry_alert_days3',
        'drug_type_id', 'strength', 'registration_no', 'tmt_id',
        'is_original_drug', 'is_antibiotic', 'max_dispense_qty',
        'indication_note', 'side_effect_note',
        'is_fda_report', 'is_fda11_report', 'is_fda13_report',
        'is_sale_control', 'sale_control_qty',
        'note', 'is_hidden', 'is_disabled',
    ];

    protected $casts = [
        'is_disabled'     => 'boolean',
        'is_hidden'       => 'boolean',
        'is_fda_report'    => 'boolean',
        'is_fda11_report'  => 'boolean',
        'is_fda13_report'  => 'boolean',
        'price_retail'    => 'decimal:2',
        'price_wholesale1'=> 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(\App\Models\ProductCategory::class);
    }

    public function lots()
    {
        return $this->hasMany(ProductLot::class);
    }

    public function drugType()
    {
        return $this->belongsTo(DrugType::class);
    }

    public function genericName()
    {
        return $this->belongsTo(DrugGenericName::class, 'drug_generic_name_id');
    }

    public function unit()
    {
        return $this->hasOne(ProductUnit::class)
            ->where(function ($query) {
                $query->where('is_base_unit', true)
                    ->orWhere('qty_per_base', 1);
            })
            ->orderByDesc('is_base_unit')
            ->orderBy('id');
    }

    public function baseUnit()
    {
        return $this->unit();
    }

    // stock คงเหลือรวมทุก lot
    public function getTotalStockAttribute()
    {
        return $this->lots->sum('qty_on_hand');
    }

    // lot ที่ต้องตัดก่อน (FEFO)
    public function getActiveLotAttribute()
    {
        return $this->lots()
            ->where('qty_on_hand', '>', 0)
            ->orderBy('expiry_date', 'asc')
            ->first();
    }
}
