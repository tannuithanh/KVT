<?php
use App\Http\Controllers\Insite;
use Illuminate\Support\Facades\Route;

//XUẤT KHO MODULE
    Route::get('/Export-Warehouse', [Insite::class, 'listExportWarehouse'])->name('listExportWarehouse')->middleware('auth');

    Route::POST('/search-supplies', [Insite::class, 'searchSupplies'])->name('searchSuppliesReal')->middleware('auth');

    Route::post('/formTrinhKy', [Insite::class, 'formTrinhKy'])->name('formTrinhKy')->middleware('auth');

    Route::get('/formTrinhKy', [Insite::class, 'formTrinhKyGet'])->name('formTrinhKyGet')->middleware('auth');

    Route::get('/xuat-Kho-Barcode', [Insite::class, 'xuatKhoBarcode'])->name('xuatKhoBarcode')->middleware('auth');

    Route::post('/xacNhanXuatKho', [Insite::class, 'xacNhanXuatKho'])->name('xacNhanXuatKho')->middleware('auth');
//XƯỞNG MODULE
    Route::get('/xuongYeuCau', [Insite::class, 'xuongYeuCau'])->name('xuongYeuCau')->middleware('auth');
