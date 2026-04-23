<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Manifest;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $tz = config('app.timezone');
        $today = Carbon::now($tz)->toDateString();

        $stats = [
            'bookings_total' => Booking::query()->count(),
            'checked_in_total' => Booking::query()->where('checked_in', true)->count(),
            'pending_checkin' => Booking::query()
                ->where('checked_in', false)
                ->whereNotIn('state', ['cancelled'])
                ->count(),
            'cancelled' => Booking::query()->where('state', 'cancelled')->count(),
            'today_bookings' => Booking::query()
                ->whereDate('booking_date', $today)
                ->whereNotIn('state', ['cancelled'])
                ->count(),
            'today_checked_in' => Booking::query()
                ->whereDate('booking_date', $today)
                ->where('checked_in', true)
                ->count(),
            'manifests_count' => Manifest::query()->count(),
        ];

        $todayLabel = Carbon::now($tz)->format('l j F Y');

        return view('admin.dashboard', compact('stats', 'todayLabel'));
    }
}
