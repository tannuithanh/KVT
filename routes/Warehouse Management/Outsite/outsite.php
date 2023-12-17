<?php

use App\Http\Controllers\Outsite;
use Illuminate\Support\Facades\Route;
Route::middleware(['auth'])->prefix('Warehouse-Management')->group(function () {
    Route::get('/brand', [Outsite::class, 'listBrand'])->name('listBrand');
    Route::get('/Project/{segment}', [Outsite::class, 'Project'])->name('listProject');
});


Route::middleware(['auth'])->prefix('Warehouse-Management-handle')->group(function () {
    Route::post('/add-project', [Outsite::class, 'addProject'])->name('addProject');
    Route::post('/edit-project', [Outsite::class, 'editProject'])->name('editProject');
    Route::post('/delete-project', [Outsite::class, 'deleteProject'])->name('deleteProject');
});
