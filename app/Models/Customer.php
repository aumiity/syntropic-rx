<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'code', 'full_name', 'id_card', 'hn',
        'dob', 'phone', 'address',
        'food_allergy', 'other_allergy', 'chronic_diseases',
        'is_alert', 'alert_note', 'warning_note',
        'is_hidden',
    ];

    protected $casts = [
        'is_alert'  => 'boolean',
        'is_hidden' => 'boolean',
        'dob'       => 'date',
    ];

    public function allergies()
    {
        return $this->hasMany(DrugAllergy::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
