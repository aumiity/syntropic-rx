<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'sort_order',
        'is_disabled',
    ];

    protected $casts = [
        'is_disabled' => 'boolean',
        'sort_order'  => 'integer',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    // scope สำหรับ query เฉพาะที่ active
    public function scopeActive($query)
    {
        return $query->where('is_disabled', false)->orderBy('sort_order');
    }
}
