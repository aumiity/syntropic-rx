<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Label Frequencies
Route::get('/label-frequencies', fn() => \App\Models\LabelFrequency::orderBy('sort_order')->get());
// Label Meal Relations
Route::get('/label-meal-relations', fn() => \App\Models\LabelMealRelation::orderBy('sort_order')->get());
// Label Dosages
Route::get('/label-dosages', fn() => \App\Models\LabelDosage::orderBy('sort_order')->get());
// Label Times
Route::get('/label-times', fn() => \App\Models\LabelTime::orderBy('sort_order')->get());
// Label Advices
Route::get('/label-advices', fn() => \App\Models\LabelAdvice::orderBy('sort_order')->get());

// Shop Settings
Route::get('/settings', fn() => \App\Models\Setting::get());
