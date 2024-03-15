<?php

use App\Http\Controllers\Insite;
use Illuminate\Support\Facades\Route;

Route::get('/ware-house-tk', [Insite::class, 'trangTonKho'])->name('trangTonKho')->middleware('auth');

Route::get('/slectedPhanKhuc', [Insite::class, 'slectedPhanKhuc'])->name('slectedPhanKhuc')->middleware('auth');
Route::get('/slectedDuAn', [Insite::class, 'slectedDuAn'])->name('slectedDuAn')->middleware('auth');
Route::get('/selectedDonHang', [Insite::class, 'selectedDonHang'])->name('selectedDonHang')->middleware('auth');

Route::Post('/listTonKho', [Insite::class, 'listTonKho'])->name('listTonKho')->middleware('auth');
