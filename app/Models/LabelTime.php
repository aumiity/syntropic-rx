<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabelTime extends Model
{
    protected $table = 'label_times';

    protected $fillable = [
        'name_th',
        'name_en',
        'name_mm',
        'name_zh',
        'sort_order',
    ];
}
