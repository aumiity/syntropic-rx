<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrugType extends Model
{
    public $timestamps = false;

    protected $fillable = ['code', 'name_th', 'is_disabled', 'is_fda9', 'is_fda10', 'is_fda11', 'is_fda13'];

    protected $casts = ['is_disabled' => 'boolean'];
}
