<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\BookingCheckInService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(
        protected BookingCheckInService $checkInService
    ) {
    }

    /**
     * Today's bookable bookings (local DB only), optionally filtered by manifest and "upcoming" window.
     */
    public function today(Request $request): JsonResponse
    {
        $tz = config('app.timezone');
        $today = Carbon::now($tz)->toDateString();

        $query = Booking::query()
            ->whereDate('booking_date', $today)
            ->whereNotIn('state', ['cancelled']);

        if ($request->filled('manifest_id')) {
            $query->where('manifest_id', $request->query('manifest_id'));
        }

        // Default false: show all of today's bookings (kiosk needs late check-ins too).
        // Pass upcoming_only=true to limit to slot start >= now.
        $upcomingOnly = $request->boolean('upcoming_only', false);

        $bookings = $query->get();

        if ($upcomingOnly) {
            $now = Carbon::now($tz);
            $bookings = $bookings
                ->filter(fn (Booking $b) => $b->startsAt()->greaterThanOrEqualTo($now))
                ->sortBy(fn (Booking $b) => $b->startsAt()->timestamp)
                ->values();
        } else {
            $bookings = $bookings->sortBy(fn (Booking $b) => $b->startsAt()->timestamp)->values();
        }

        $data = $bookings->map(fn (Booking $b) => [
            'booking_id' => $b->booking_id,
            'customer_name' => $b->customer_name,
            'booking_time' => $b->displayTime(),
            'duration' => $b->duration,
            'manifest_name' => $b->manifest_name,
            'state' => $b->state,
            'checked_in' => $b->checked_in,
        ]);

        return response()->json($data);
    }

    /**
     * Check in a booking by Periode booking_id (string).
     */
    public function checkIn(Request $request, string $bookingId): JsonResponse
    {
        try {
            $booking = $this->checkInService->checkIn($bookingId);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Booking not found.'], 404);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getStatusCode());
        }

        return response()->json([
            'booking_id' => $booking->booking_id,
            'customer_name' => $booking->customer_name,
            'booking_time' => $booking->displayTime(),
            'duration' => $booking->duration,
            'manifest_name' => $booking->manifest_name,
            'state' => $booking->state,
            'checked_in' => $booking->checked_in,
            'checked_in_at' => $booking->checked_in_at?->toIso8601String(),
        ]);
    }
}
