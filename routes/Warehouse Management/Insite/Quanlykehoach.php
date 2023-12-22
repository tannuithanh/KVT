<?php

use App\Http\Controllers\Insite;
use Illuminate\Support\Facades\Route;
    Route::get('/ware-house/{project}', [Insite::class, 'listWarehouse'])->name('listWarehouse')->middleware('auth');
    Route::POST('/import-supplies', [Insite::class, 'importSupplies'])->name('importSupplies')->middleware('auth');


    Route::POST('/importThuCong', [Insite::class, 'importThuCong'])->name('importThuCong')->middleware('auth');