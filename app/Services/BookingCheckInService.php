<?php

namespace App\Services;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BookingCheckInService
{
    /**
     * Mark a booking as checked in. Idempotent if already checked in.
     */
    public function checkIn(string $bookingId): Booking
    {
        $booking = Booking::query()->where('booking_id', $bookingId)->first();

        if (! $booking) {
            throw (new ModelNotFoundException)->setModel(Booking::class, [$bookingId]);
        }

        if ($booking->state === 'cancelled') {
            throw new HttpException(422, 'Cancelled bookings cannot be checked in.');
        }

        // Must match the same calendar day as the list endpoint (APP_TIMEZONE / Periode business day).
        $todayStr = Carbon::now(config('app.timezone'))->toDateString();
        if ($booking->booking_date->format('Y-m-d') !== $todayStr) {
            throw new HttpException(422, 'Only today\'s bookings can be checked in.');
        }

        if ($booking->checked_in) {
            return $booking;
        }

        $booking->forceFill([
            'checked_in' => true,
            'checked_in_at' => Carbon::now(),
        ])->save();

        return $booking->fresh();
    }
}
