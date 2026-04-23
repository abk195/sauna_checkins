<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Manifest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class BookingSyncService
{
    public function __construct(
        protected PeriodeApiService $apiService
    ) {}

    /**
     * Execute the full bookings sync for all manifests and dates in the window.
     */
    public function sync(): void
    {
        $startedAt = microtime(true);

        $syncWindow = Config::get('periode.sync_window', [
            'days_back' => 1,
            'days_forward' => 7,
        ]);

        $daysBack = (int) ($syncWindow['days_back'] ?? 1);
        $daysForward = (int) ($syncWindow['days_forward'] ?? 7);

        $startDate = Carbon::today()->subDays($daysBack);
        $endDate = Carbon::today()->addDays($daysForward);

        $manifests = Manifest::query()->orderBy('name')->get();

        Log::info('Booking sync started.', [
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'days_back' => $daysBack,
            'days_forward' => $daysForward,
            'manifest_count' => $manifests->count(),
        ]);

        if ($manifests->isEmpty()) {
            Log::warning('Booking sync skipped: no manifests in database.');

            return;
        }

        foreach ($this->dateRange($startDate, $endDate) as $date) {
            foreach ($manifests as $manifest) {
                $manifestId = $manifest->manifest_id;
                $manifestName = $manifest->name;

                Log::info('Syncing bookings for manifest.', [
                    'manifest_id' => $manifestId,
                    'manifest_name' => $manifestName,
                    'date' => $date->toDateString(),
                ]);

                $bookings = $this->apiService->getBookings($manifestId, $date->toDateString());

                Log::info('Bookings fetched from API.', [
                    'manifest_id' => $manifestId,
                    'date' => $date->toDateString(),
                    'count' => count($bookings),
                ]);

                foreach ($bookings as $bookingData) {
                    $this->upsertBooking($bookingData, $manifestId, $manifestName);
                }
            }
        }

        $duration = microtime(true) - $startedAt;

        Log::info('Booking sync completed.', [
            'duration_seconds' => $duration,
        ]);
    }

    /**
     * Generate an inclusive Carbon date range.
     *
     * @return \Generator|Carbon[]
     */
    protected function dateRange(Carbon $start, Carbon $end): \Generator
    {
        $current = $start->copy();

        while ($current->lessThanOrEqualTo($end)) {
            yield $current->copy();
            $current->addDay();
        }
    }

    /**
     * Upsert a booking record from API payload.
     */
    protected function upsertBooking(array $bookingData, string $manifestId, ?string $manifestName): void
    {
        // The exact field names from the Periode API are assumed here and
        // can be adjusted once the real API contract is confirmed.
        $bookingId = $bookingData['id'] ?? null;

        if (! $bookingId) {
            Log::warning('Skipping booking without id.', [
                'manifest_id' => $manifestId,
                'payload' => $bookingData,
            ]);

            return;
        }

        $merchantId = Config::get('periode.merchant_id');

        $state = $bookingData['state'] ?? ($bookingData['status'] ?? 'unknown');

        $priceCents = $bookingData['price'] ?? ($bookingData['amount'] ?? null);
        $price = null;

        if ($priceCents !== null) {
            $price = ((float) $priceCents) / 100;
        }

        $bookingDate = $bookingData['date'] ?? $bookingData['booking_date'] ?? null;
        $bookingTime = $bookingData['time'] ?? $bookingData['booking_time'] ?? null;

        $user = $bookingData['user'] ?? [];
        $payload = $bookingData;

        $attributes = [
            'booking_id' => (string) $bookingId,
        ];

        $values = [
            'manifest_id' => $manifestId,
            'manifest_name' => $manifestName,
            'merchant_id' => $merchantId,
            'customer_name' => $user['name'] ?? null,
            'customer_email' => $user['email'] ?? null,
            'customer_phone' => $user['phoneNumber'] ?? null,
            'booking_date' => $bookingDate,
            'booking_time' => $bookingTime,
            'duration' => $bookingData['length'] ?? ($bookingData['duration'] ?? null),
            'price' => $price,
            'currency' => $bookingData['currency'] ?? null,
            'payment_type' => $bookingData['paymentType'] ?? null,
            'state' => $state,
            'raw_payload' => $payload,
            'synced_at' => Carbon::now(),
        ];

        try {
            /** @var \App\Models\Booking $booking */
            $booking = Booking::updateOrCreate($attributes, $values);

            Log::info('Booking upserted.', [
                'booking_id' => $booking->booking_id,
                'state' => $booking->state,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to upsert booking.', [
                'booking_id' => $bookingId,
                'exception' => $e->getMessage(),
            ]);
        }
    }
}
