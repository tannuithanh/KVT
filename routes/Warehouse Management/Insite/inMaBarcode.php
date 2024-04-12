<?php

use App\Http\Controllers\inMaBarcodeController;
use App\Http\Controllers\Insite;
use Illuminate\Support\Facades\Route;

Route::get('/inMaBarcode', [inMaBarcodeController::class, 'inMaBarcode'])->name('inMaBarcode')->middleware('auth');

Route::post('/timKiemMaBarcode', [inMaBarcodeController::class, 'timKiemMaBarcode'])->name('timKiemMaBarcode')->middleware('auth');
