<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrugGenericName extends Model
{
    protected $fillable = [
        'code',
        'name',
        'indication_note',
        'is_antibiotic',
        'is_pregnancy_lactation',
        'is_pregnancy_category',
        'pregnancy_category_type_key',
        'drug_group_id',
        'is_disabled',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'drug_generic_name_id');
    }
}
