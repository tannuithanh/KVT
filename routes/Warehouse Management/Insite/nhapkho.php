<?php
use App\Http\Controllers\Insite;
use Illuminate\Support\Facades\Route;
    Route::get('/ware-house-nk', [Insite::class, 'listNhapKho'])->name('listNhapKho')->middleware('auth');

    Route::POST('/layThongTin-DonHang', [Insite::class, 'layThongTinDonHang'])->name('layThongTinDonHang')->middleware('auth');

    Route::POST('/luuInMaBarcode', [Insite::class, 'luuInMaBarcode'])->name('luuInMaBarcode')->middleware('auth');

    Route::get('/laySoLuongKho', [Insite::class, 'laySoLuongKho'])->name('laySoLuongKho')->middleware('auth');

    Route::get('/quetBarcodeNhapKho', [Insite::class, 'quetBarcodeNhapKho'])->name('quetBarcodeNhapKho')->middleware('auth');

    Route::POST('/KiemTraSoluongTruocKhiNhapKho', [Insite::class, 'KiemTraSoluongTruocKhiNhapKho'])->name('KiemTraSoluongTruocKhiNhapKho')->middleware('auth');

    Route::POST('/KiemTraSoluongTruocKhiNhapKhoV2', [Insite::class, 'KiemTraSoluongTruocKhiNhapKhoV2'])->name('KiemTraSoluongTruocKhiNhapKhoV2')->middleware('auth');

    Route::POST('/update-Quanlity', [Insite::class, 'updateQuanlity'])->name('updateQuanlity')->middleware('auth');

    Route::POST('/updateVatTuNhap', [Insite::class, 'updateVatTuNhap'])->name('updateVatTuNhap')->middleware('auth');
