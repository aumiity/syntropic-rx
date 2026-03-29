<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DosageForm extends Model
{
    public $timestamps = false;

    protected $fillable = ['name_th', 'name_en', 'is_disabled'];

    protected $casts = ['is_disabled' => 'boolean'];
}
