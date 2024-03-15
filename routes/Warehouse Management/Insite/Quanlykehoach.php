<?php

use App\Http\Controllers\Insite;
use Illuminate\Support\Facades\Route;
    Route::get('/ware-house/{project}', [Insite::class, 'listWarehouse'])->name('listWarehouse')->middleware('auth');

    Route::POST('/import-supplies', [Insite::class, 'importSupplies'])->name('importSupplies')->middleware('auth');

    Route::POST('/soDonHangvaNCC', [Insite::class, 'soDonHangvaNCC'])->name('soDonHangvaNCC')->middleware('auth');

    Route::POST('/importThuCong', [Insite::class, 'importThuCong'])->name('importThuCong')->middleware('auth');

    Route::POST('/addquantity', [Insite::class, 'addQuantity'])->name('addQuantity')->middleware('auth');

    Route::post('/themdonhangthucong', [Insite::class, 'themdonhangthucong'])->name('themdonhangthucong')->middleware('auth');

    Route::post('/deleteDonHang', [Insite::class, 'deleteDonHang'])->name('deleteDonHang')->middleware('auth');

    Route::POST('/suavattu', [Insite::class, 'suavattu'])->name('suavattu')->middleware('auth');

    Route::POST('/dulieuvattuchitiet', [Insite::class, 'duLieuVatTuChiTiet'])->name('DuLieuVatTuChiTiet')->middleware('auth');

    Route::POST('/xoavattuchitiet', [Insite::class, 'xoavattuchitiet'])->name('xoavattuchitiet')->middleware('auth');

    Route::POST('/themvattuchitiet', [Insite::class, 'themvattuchitiet'])->name('themvattuchitiet')->middleware('auth');

    Route::POST('/suavattuchitiet', [Insite::class, 'suavattuchitiet'])->name('suavattuchitiet')->middleware('auth');

    Route::POST('/lichsuvattu', [Insite::class, 'lichsuvattu'])->name('lichsuvattu')->middleware('auth');

    Route::POST('/timkiemvattuchitiet', [Insite::class, 'timkiemvattuchitiet'])->name('timkiemvattuchitiet')->middleware('auth');
