<?php
use App\Http\Controllers\Insite;
use Illuminate\Support\Facades\Route;
    Route::get('/ware-house-nk/{project}', [Insite::class, 'listNhapKho'])->name('listNhapKho')->middleware('auth');

    Route::POST('/nhapkho', [Insite::class, 'nhapKho'])->name('nhapKho')->middleware('auth');

    Route::get('/laySoLuongKho', [Insite::class, 'laySoLuongKho'])->name('laySoLuongKho')->middleware('auth');

    Route::POST('/laythongtinvattu', [Insite::class, 'laythongtinvattu'])->name('laythongtinvattu')->middleware('auth');

    Route::get('/quetBarcodeNhapKho', [Insite::class, 'quetBarcodeNhapKho'])->name('quetBarcodeNhapKho')->middleware('auth');
