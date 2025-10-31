<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LeadFilterController;
use App\Http\Controllers\PullController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'show'])->name('crm.login.show');
Route::post('/login', [AuthController::class, 'store'])->name('crm.login.store');
Route::post('/logout', [AuthController::class, 'destroy'])->middleware('crm.auth')->name('crm.logout');

Route::middleware('crm.auth')->group(function () {
    Route::view('/', 'dashboard')->name('dashboard');
    // BigQuery counts and preview
    Route::post('/leads/counts', [LeadFilterController::class, 'counts'])->name('leads.counts');
    Route::get('/leads', [LeadFilterController::class, 'index'])->name('leads.index');

    // Pull to destination API
    Route::post('/pull/start', [PullController::class, 'start'])->name('pull.start');
    Route::get('/pull/{pullJob}/status', [PullController::class, 'status'])->name('pull.status');
});
