<?php

use App\Http\Controllers\DashBoard;
use Illuminate\Support\Facades\Route;
    Route::get('/Dash-Board', [DashBoard::class, 'dashboard'])->name('dashboard')->middleware('auth');