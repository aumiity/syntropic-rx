<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemUnit extends Model
{
    protected $fillable = ['name', 'multiply'];

    protected $casts = [
        'multiply' => 'decimal:4',
    ];
}