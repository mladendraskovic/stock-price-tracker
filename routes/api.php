<?php

use App\Http\Controllers\StockController;
use Illuminate\Support\Facades\Route;

Route::get('/stocks', [StockController::class, 'index']);
Route::get('/stocks/multiple', [StockController::class, 'multiple']);
Route::get('/stocks/{symbol}', [StockController::class, 'show']);
