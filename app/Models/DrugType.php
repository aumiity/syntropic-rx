<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrugType extends Model
{
    public $timestamps = false;

    protected $fillable = ['code', 'name_th', 'khor_yor_report', 'is_disabled'];

    protected $casts = ['is_disabled' => 'boolean'];
}
