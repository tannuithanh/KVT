<?php

use App\Http\Controllers\Insite;
use Illuminate\Support\Facades\Route;

//ĐƠN HÀNG
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
//DANH MỤC VẬT TƯ
    Route::POST('/vattutrongdanhmuc', [Insite::class, 'vattutrongdanhmuc'])->name('vattutrongdanhmuc')->middleware('auth');

    Route::POST('/xoa_Danhmuc', [Insite::class, 'xoa_Danhmuc'])->name('xoa_Danhmuc')->middleware('auth');

    Route::POST('/thayTheVatTu', [Insite::class, 'thayTheVatTu'])->name('thayTheVatTu')->middleware('auth');

    Route::POST('/timKiem_VatTu', [Insite::class, 'timKiem_VatTu'])->name('timKiemVatTu')->middleware('auth');

//QUẢN LÝ ĐƠN HANG (KẾ HOẠCH)

    Route::get('/quanLyDonHang', [Insite::class, 'quanLyDonHang'])->name('quanLyDonHang')->middleware('auth');

    Route::post('/layDanhMucChuaCoDonHang', [Insite::class, 'layDanhMucChuaCoDonHang'])->name('layDanhMucChuaCoDonHang')->middleware('auth');

    Route::post('/duLieuVatTuCuaDanhMuc', [Insite::class, 'duLieuVatTuCuaDanhMuc'])->name('duLieuVatTuCuaDanhMuc')->middleware('auth');

    Route::post('/taoDonHangMoi', [Insite::class, 'taoDonHangMoi'])->name('taoDonHangMoi')->middleware('auth');

    Route::post('/huyDonHang', [Insite::class, 'huyDonHang'])->name('huyDonHang')->middleware('auth');

    Route::post('/suaDonHang', [Insite::class, 'suaDonHang'])->name('suaDonHang')->middleware('auth');

    Route::post('/capNhatDonHang', [Insite::class, 'capNhatDonHang'])->name('capNhatDonHang')->middleware('auth');

    Route::post('/timKiemDanhMucVatTu', [Insite::class, 'timKiemDanhMucVatTu'])->name('timKiemDanhMucVatTu')->middleware('auth');

    Route::post('/timKiemVatTuOfDM', [Insite::class, 'timKiemVatTuOfDM'])->name('timKiemVatTuOfDM')->middleware('auth');
