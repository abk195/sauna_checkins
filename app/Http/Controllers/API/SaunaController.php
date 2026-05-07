<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Sauna;
use App\Services\SaunaActiveSessionResolver;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class SaunaController extends Controller
{
    public function __construct(
        protected SaunaActiveSessionResolver $resolver,
    ) {}

    /**
     * Return the currently-active manifest for a sauna along with its bookings.
     * The Vue iPad UI polls this so the active session can roll over automatically.
     */
    public function active(Sauna $sauna): JsonResponse
    {
        $tz = config('app.timezone');
        $now = Carbon::now($tz);

        $resolved = $this->resolver->resolve($sauna, $now);

        $activeManifest = $resolved['active_manifest'];
        $sessionStart = $resolved['active_session_start'];

        $pastGraceMinutes = (int) config('periode.checkin_past_grace_minutes', 60);
        $cutoff = $now->copy()->subMinutes(max($pastGraceMinutes, 0));

        $bookingsPayload = $resolved['bookings']
            ->filter(fn (Booking $b) => $b->startsAt()->greaterThanOrEqualTo($cutoff))
            ->values()
            ->map(fn (Booking $b) => [
                'booking_id' => $b->booking_id,
                'customer_name' => $b->customer_name,
                'booking_time' => $b->displayTime(),
                'duration' => $b->duration,
                'manifest_name' => $b->manifest_name,
                'state' => $b->state,
                'checked_in' => $b->checked_in,
            ]);

        return response()->json([
            'sauna' => [
                'id' => $sauna->id,
                'name' => $sauna->name,
                'slug' => $sauna->slug,
            ],
            'active_manifest' => $activeManifest ? [
                'manifest_id' => $activeManifest->manifest_id,
                'name' => $activeManifest->name,
                'session_start' => $sessionStart?->toIso8601String(),
            ] : null,
            'bookings' => $bookingsPayload,
            'server_time' => $now->toIso8601String(),
        ]);
    }
}
