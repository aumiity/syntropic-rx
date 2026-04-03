<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Label Frequencies
Route::get('/label-frequencies', fn() => \App\Models\LabelFrequency::orderBy('sort_order')->get());
// Label Timings
Route::get('/label-timings', fn() => \App\Models\LabelTiming::orderBy('sort_order')->get());
