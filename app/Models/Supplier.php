<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'old_vendor_key', 'code', 'name', 'tax_id', 'phone', 'address', 'contact_name', 'is_disabled',
    ];

    protected $casts = [
        'is_disabled' => 'boolean',
    ];
}
