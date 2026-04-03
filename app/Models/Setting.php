<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'settings';

    protected $fillable = [
        'shop_name',
        'shop_address',
        'shop_phone',
        'shop_license_no',
        'shop_line_id',
        'shop_tax_id',
    ];

    /**
     * Get the singleton settings record
     */
    public static function get(): self
    {
        return static::firstOrCreate(['id' => 1]);
    }
}
