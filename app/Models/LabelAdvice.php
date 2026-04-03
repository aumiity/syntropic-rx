<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabelAdvice extends Model
{
    protected $table = 'label_advices';

    protected $fillable = [
        'name_th',
        'name_en',
        'name_mm',
        'name_zh',
        'sort_order',
    ];
}
