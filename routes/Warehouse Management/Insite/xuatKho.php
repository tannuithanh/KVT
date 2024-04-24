<?php
use App\Http\Controllers\Insite;
use Illuminate\Support\Facades\Route;

    Route::get('/Export-Warehouse/{project}', [Insite::class, 'listExportWarehouse'])->name('listExportWarehouse')->middleware('auth');

    Route::get('/search-supplies', [Insite::class, 'searchSupplies'])->name('searchSuppliesReal')->middleware('auth');

    Route::post('/formTrinhKy', [Insite::class, 'formTrinhKy'])->name('formTrinhKy')->middleware('auth');

    Route::get('/formTrinhKy', [Insite::class, 'formTrinhKyGet'])->name('formTrinhKyGet')->middleware('auth');

    Route::get('/xuat-Kho-Barcode', [Insite::class, 'xuatKhoBarcode'])->name('xuatKhoBarcode')->middleware('auth');

    Route::post('/checkVatTuXuatKho', [Insite::class, 'checkVatTuXuatKho'])->name('checkVatTuXuatKho')->middleware('auth');

    Route::post('/xacNhanXuatKho', [Insite::class, 'xacNhanXuatKho'])->name('xacNhanXuatKho')->middleware('auth');
