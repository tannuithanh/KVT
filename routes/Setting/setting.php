<?php

use App\Http\Controllers\Setting;
use Illuminate\Support\Facades\Route;
    Route::get('/profile', [Setting::class, 'profile'])->name('profile')->middleware('auth');
    Route::post('/change-password', [Setting::class, 'changePassword'])->name('changePassword')->middleware('auth');
    Route::get('/setting', [Setting::class, 'setting'])->name('setting')->middleware('auth');

    Route::middleware(['auth'])->prefix('system-installation')->group(function () {
        Route::get('/position', [Setting::class, 'listPosition'])->name('listPosition')->middleware('auth');
        Route::get('/department', [Setting::class, 'listDepartment'])->name('listDepartment')->middleware('auth');
        Route::POST('/department-edit', [Setting::class, 'editDepartment'])->name('editDepartment')->middleware('auth');
        Route::get('/user', [Setting::class, 'listUser'])->name('listUser')->middleware('auth');
        Route::Post('/edit-user', [Setting::class, 'editUser'])->name('editUser')->middleware('auth');
        Route::Post('/delete-user', [Setting::class, 'deleteUser'])->name('deleteUser')->middleware('auth');
        Route::Post('/add-user', [Setting::class, 'addUser'])->name('addUser')->middleware('auth');
        Route::get('/providers', [Setting::class, 'listProviderDetail'])->name('listProviderDetail')->middleware('auth');
        Route::POST('/add-providers', [Setting::class, 'addProviderDetail'])->name('addProviderDetail')->middleware('auth');
        Route::POST('/edit-providers', [Setting::class, 'editProviderDetail'])->name('editProviderDetail')->middleware('auth');
        Route::POST('/delete-providers', [Setting::class, 'deleteProviderDetail'])->name('deleteProviderDetail')->middleware('auth');
    });