<?php

use App\Http\Controllers\CheckinManifestController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\CheckinUserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ManifestController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::redirect('/', '/checkin');

Route::get('/checkin', function () {
    return view('checkin');
})->name('checkin');

Route::get('/manifest/{manifest_id}', CheckinManifestController::class)
    ->name('checkin.manifest');

Route::get('/welcome', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/admin/login', [LoginController::class, 'login'])
        ->middleware('throttle:10,1');
});

Route::middleware('auth')->group(function () {
    Route::get('/admin', function () {
        return redirect()->route('admin.dashboard');
    });

    Route::post('/admin/logout', [LoginController::class, 'logout'])->name('logout');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
        Route::get('/checkin-users', [CheckinUserController::class, 'index'])->name('checkin-users.index');
        Route::resource('manifests', ManifestController::class)->except(['show']);
    });
});
