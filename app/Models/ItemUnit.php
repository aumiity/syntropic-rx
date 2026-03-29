<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemUnit extends Model
{
    public $timestamps = false;

    protected $table = 'item_units';

    protected $fillable = ['name', 'multiply'];
}
