<?php
use App\Http\Controllers\Insite;
use Illuminate\Support\Facades\Route;

Route::get('/Check-quality/{id}', [Insite::class, 'checkQuality'])->name('checkQuality')->middleware('auth');

Route::POST('/luuKiemTraChatLuong', [Insite::class, 'luuKiemTraChatLuong'])->name('luuKiemTraChatLuong')->middleware('auth');

Route::POST('/timKiemVatTuCheck', [Insite::class, 'timKiemVatTuCheck'])->name('timKiemVatTuCheck')->middleware('auth');
