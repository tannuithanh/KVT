<?php
use App\Http\Controllers\Insite;
use Illuminate\Support\Facades\Route;
    Route::get('/ware-house-nk/{project}', [Insite::class, 'listNhapKho'])->name('listNhapKho')->middleware('auth');