<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn() => response()->json([
    'app' => 'Car Electrical Loyalty API',
    'version' => '1.0.0',
    'status' => 'ok',
]));