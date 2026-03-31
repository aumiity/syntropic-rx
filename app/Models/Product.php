    public function productUnits()
    {
        return $this->hasMany(ProductUnit::class);
    }
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'barcode', 'code', 'trade_name', 'name_for_print',
        'category_id', 'unit_id', 'unit_small_id', 'unit_large_id', 'conversion', 'price_retail', 'price_wholesale1',
        'reorder_point', 'safety_stock','search_keywords',
        'expiry_alert_days1', 'expiry_alert_days2', 'expiry_alert_days3',
        'drug_type_id', 'is_fda_report', 'is_fda13_report',
        'is_disabled', 'is_hidden','default_qty',
    ];

    protected $casts = [
        'is_disabled'     => 'boolean',
        'is_hidden'       => 'boolean',
        'is_fda_report'   => 'boolean',
        'is_fda13_report' => 'boolean',
        'price_retail'    => 'decimal:2',
        'price_wholesale1'=> 'decimal:2',
        'conversion'      => 'integer',
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

    public function unit()
    {
        return $this->belongsTo(ItemUnit::class);
    }

    public function unitSmall()
    {
        return $this->belongsTo(ItemUnit::class, 'unit_small_id');
    }

    public function unitLarge()
    {
        return $this->belongsTo(ItemUnit::class, 'unit_large_id');
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
