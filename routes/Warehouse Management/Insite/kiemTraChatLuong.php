<?php
use App\Http\Controllers\Insite;
use Illuminate\Support\Facades\Route;

Route::get('/kiemTraCLBarcode', [Insite::class, 'kiemTraCLBarcode'])->name('kiemTraCLBarcode')->middleware('auth');

Route::get('/Check-quality', [Insite::class, 'checkQuality'])->name('checkQuality')->middleware('auth');

Route::POST('/luuKiemTraChatLuong', [Insite::class, 'luuKiemTraChatLuong'])->name('luuKiemTraChatLuong')->middleware('auth');

Route::POST('/timKiemVatTuCheck', [Insite::class, 'timKiemVatTuCheck'])->name('timKiemVatTuCheck')->middleware('auth');

Route::POST('/takeIdByBarcode', [Insite::class, 'takeIdByBarcode'])->name('takeIdByBarcode')->middleware('auth');
