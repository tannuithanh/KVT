<?php

use App\Http\Controllers\Insite;
use Illuminate\Support\Facades\Route;
    Route::get('/ware-house/{project}', [Insite::class, 'listWarehouse'])->name('listWarehouse')->middleware('auth');


