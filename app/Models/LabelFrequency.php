<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabelFrequency extends Model
{
    protected $table = 'label_frequencies';

    protected $fillable = [
        'code',
        'name_th',
        'name_en',
        'name_mm',
        'name_zh',
        'sort_order',
    ];
}
