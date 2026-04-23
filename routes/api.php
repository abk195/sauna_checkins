<?php

use App\Http\Controllers\API\BookingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/bookings/today', [BookingController::class, 'today']);
Route::post('/bookings/{bookingId}/check-in', [BookingController::class, 'checkIn'])
    ->where('bookingId', '[A-Za-z0-9_-]+');
