<?php

use Illuminate\Support\Facades\Route;

// مسیر اصلی (برای تست API)
Route::get('/', fn() => response()->json([
    'app' => 'Car Electrical Loyalty API',
    'version' => '1.0.0',
    'status' => 'ok',
]));

// مسیر Health Check برای Render
// این مسیر به Render می‌گوید که سرویس سالم است
Route::get('/health', fn() => response()->json([
    'status' => 'ok',
], 200));