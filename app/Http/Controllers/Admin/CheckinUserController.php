<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Manifest;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckinUserController extends Controller
{
    /**
     * Bookings that have been checked in at the kiosk, newest first.
     */
    public function index(Request $request): View
    {
        $request->validate([
            'state' => ['nullable', 'string', 'max:255'],
            'manifest_id' => ['nullable', 'string', 'max:255'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
        ]);

        if ($request->filled('date_from') && $request->filled('date_to') && $request->date_from > $request->date_to) {
            return redirect()
                ->back()
                ->withErrors(['date_to' => 'End date must be on or after start date.'])
                ->withInput();
        }

        $query = Booking::query()
            ->with('manifest')
            ->where('checked_in', true);

        if ($request->filled('state')) {
            $query->where('state', $request->string('state'));
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
            ->orderByDesc('checked_in_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $stateOptions = Booking::query()
            ->where('checked_in', true)
            ->whereNotNull('state')
            ->where('state', '!=', '')
            ->distinct()
            ->orderBy('state')
            ->pluck('state');

        $manifestOptions = Manifest::query()->orderBy('name')->get();

        $filters = [
            'state' => (string) $request->input('state', ''),
            'manifest_id' => (string) $request->input('manifest_id', ''),
            'date_from' => (string) $request->input('date_from', ''),
            'date_to' => (string) $request->input('date_to', ''),
        ];

        return view('admin.checkin_users.index', compact(
            'bookings',
            'stateOptions',
            'manifestOptions',
            'filters'
        ));
    }
}
