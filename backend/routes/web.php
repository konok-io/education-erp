<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// API Routes
Route::get('/api', function () {
    return response()->json([
        'name' => 'Education ERP API',
        'version' => '1.0.0',
        'status' => 'running',
    ]);
});
