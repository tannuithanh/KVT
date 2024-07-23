<?php

use App\Http\Controllers\DashBoard;
use Illuminate\Support\Facades\Route;
    Route::get('/trangChu', [DashBoard::class, 'trangChu'])->name('trangChu')->middleware('auth');
    Route::get('/Dash-Board', [DashBoard::class, 'dashBoard'])->name('dashBoard')->middleware('auth');

    Route::get('/orders-by-DB', [DashBoard::class, 'orderByDashboard'])->name('orderByDashboard')->middleware('auth');
