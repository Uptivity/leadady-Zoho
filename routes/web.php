<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'show'])->name('crm.login.show');
Route::post('/login', [AuthController::class, 'store'])->name('crm.login.store');
Route::post('/logout', [AuthController::class, 'destroy'])->middleware('crm.auth')->name('crm.logout');

Route::middleware('crm.auth')->group(function () {
    Route::view('/', 'dashboard')->name('dashboard');
});
