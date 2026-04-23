<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Manifest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'state' => ['nullable', 'string', 'max:255'],
            'manifest_id' => ['nullable', 'string', 'max:255'],
            'payment_type' => ['nullable', 'string', 'max:255'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
        ]);

        if ($request->filled('date_from') && $request->filled('date_to') && $request->date_from > $request->date_to) {
            return redirect()
                ->back()
                ->withErrors(['date_to' => 'End date must be on or after start date.'])
                ->withInput();
        }

        $query = Booking::query()->with('manifest');

        if ($request->filled('state')) {
            $query->where('state', $request->string('state'));
        }

        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->string('payment_type'));
        }

        if ($request->filled('manifest_id')) {
            $query->where('manifest_id', $request->string('manifest_id'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('booking_date', '>=', $request->date('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('booking_date', '<=', $request->date('date_to'));
        }

        $bookings = $query
            ->orderByDesc('booking_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $stateOptions = Booking::query()
            ->whereNotNull('state')
            ->where('state', '!=', '')
            ->distinct()
            ->orderBy('state')
            ->pluck('state');

        $paymentOptions = Booking::query()
            ->whereNotNull('payment_type')
            ->where('payment_type', '!=', '')
            ->distinct()
            ->orderBy('payment_type')
            ->pluck('payment_type');

        $manifestOptions = Manifest::query()->orderBy('name')->get();

        $filters = [
            'state' => (string) $request->input('state', ''),
            'manifest_id' => (string) $request->input('manifest_id', ''),
            'payment_type' => (string) $request->input('payment_type', ''),
            'date_from' => (string) $request->input('date_from', ''),
            'date_to' => (string) $request->input('date_to', ''),
        ];

        return view('admin.bookings.index', compact(
            'bookings',
            'stateOptions',
            'manifestOptions',
            'paymentOptions',
            'filters'
        ));
    }

    public function show(Booking $booking): JsonResponse
    {
        $booking->load('manifest');

        return response()->json([
            'data' => $this->detailPayload($booking),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function detailPayload(Booking $booking): array
    {
        $m = $booking->manifest;

        return [
            'id' => $booking->id,
            'booking_id' => $booking->booking_id,
            'manifest_id' => $booking->manifest_id,
            'manifest_from_db' => $m ? [
                'id' => $m->id,
                'manifest_id' => $m->manifest_id,
                'name' => $m->name,
            ] : null,
            'manifest_name_on_booking' => $booking->manifest_name,
            'merchant_id' => $booking->merchant_id,
            'customer_name' => $booking->customer_name,
            'customer_email' => $booking->customer_email,
            'customer_phone' => $booking->customer_phone,
            'booking_date' => $booking->booking_date?->toDateString(),
            'booking_time_raw' => $booking->booking_time !== null
                ? (string) $booking->booking_time
                : null,
            'booking_time_display' => $booking->displayTime(),
            'duration' => $booking->duration,
            'price' => $booking->price !== null ? (float) $booking->price : null,
            'price_formatted' => $booking->price !== null
                ? number_format((float) $booking->price, 2, '.', '').($booking->currency ? ' '.$booking->currency : '')
                : null,
            'currency' => $booking->currency,
            'payment_type' => $booking->payment_type,
            'state' => $booking->state,
            'checked_in' => $booking->checked_in,
            'checked_in_at' => $booking->checked_in_at?->toIso8601String(),
            'checked_in_at_local' => $booking->checked_in_at?->timezone(config('app.timezone'))->format('Y-m-d H:i'),
            'synced_at' => $booking->synced_at?->toIso8601String(),
            'created_at' => $booking->created_at?->toIso8601String(),
            'updated_at' => $booking->updated_at?->toIso8601String(),
            'raw_payload' => $booking->raw_payload,
        ];
    }
}
