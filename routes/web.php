<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\StoreHourController;
use App\Http\Controllers\Admin\PageController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

/*Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';*/


Route::get('/store-status', [StoreHourController::class, 'getStoreStatus']);
Route::get('/next-opening', [StoreHourController::class, 'getNextOpening']);


/**
 * Admin
 *
 * Since this is just a simple setup, I didn't create login for authentication.
 */
Route::get('/admin/store-hours', [PageController::class, 'index'])->name('admin.store-hours');
Route::patch('/admin/store-hours/{storeHour}', [PageController::class, 'updateStoreHours'])->name('admin.store-hours.update');
