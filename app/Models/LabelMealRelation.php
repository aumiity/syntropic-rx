<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabelMealRelation extends Model
{
    protected $table = 'label_meal_relations';

    protected $fillable = [
        'code',
        'name_th',
        'name_en',
        'name_mm',
        'name_zh',
        'sort_order',
    ];
}
