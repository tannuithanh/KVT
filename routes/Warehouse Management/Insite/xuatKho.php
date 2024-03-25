<?php
use App\Http\Controllers\Insite;
use Illuminate\Support\Facades\Route;

    Route::get('/Export-Warehouse/{project}', [Insite::class, 'listExportWarehouse'])->name('listExportWarehouse')->middleware('auth');
