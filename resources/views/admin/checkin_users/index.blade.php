@extends('layouts.admin')

@section('title', 'Checkin Users')

@section('heading', 'Checkin Users')

@section('content')
    @php
        $tz = config('app.timezone');
    @endphp
    <p style="color: var(--muted); margin: 0 0 1rem; font-size: 0.95rem;">
        Guests who have checked in via the kiosk. Sorted by check-in time (newest first). Times use <strong>{{ $tz }}</strong>.
        Date range filters apply to <strong>booking date</strong>.
    </p>

    <form method="get" action="{{ route('admin.checkin-users.index') }}" class="filter-bar card" style="margin-bottom: 1.25rem;">
        <div class="filter-bar__grid">
            <div class="field" style="margin-bottom: 0;">
                <label for="filter_manifest">Manifest</label>
                <select id="filter_manifest" name="manifest_id">
                    <option value="" @selected(old('manifest_id', $filters['manifest_id'] ?? '') === '')>All manifests</option>
                    @foreach ($manifestOptions as $m)
                        <option value="{{ $m->manifest_id }}" @selected(old('manifest_id', $filters['manifest_id'] ?? '') === $m->manifest_id)>
                            {{ $m->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="field" style="margin-bottom: 0;">
                <label for="filter_state">State</label>
                <select id="filter_state" name="state">
                    <option value="" @selected(old('state', $filters['state'] ?? '') === '')>All states</option>
                    @foreach ($stateOptions as $s)
                        <option value="{{ $s }}" @selected(old('state', $filters['state'] ?? '') === $s)>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field" style="margin-bottom: 0;">
                <label for="filter_date_from">Booking date from</label>
                <input id="filter_date_from" type="date" name="date_from" value="{{ old('date_from', $filters['date_from'] ?? '') }}">
            </div>
            <div class="field" style="margin-bottom: 0;">
                <label for="filter_date_to">Booking date to</label>
                <input id="filter_date_to" type="date" name="date_to" value="{{ old('date_to', $filters['date_to'] ?? '') }}">
            </div>
        </div>
        <div class="filter-bar__actions">
            <button type="submit" class="btn">Apply filters</button>
            <a href="{{ route('admin.checkin-users.index') }}" class="btn btn-ghost" style="width: auto;">Clear</a>
        </div>
    </form>

    <div class="table-wrap card" style="padding: 0;">
        <table>
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Email</th>
                    <th>Booking date</th>
                    <th>Slot time</th>
                    <th>Booking ID</th>
                    <th>Manifest</th>
                    <th>Duration</th>
                    <th>State</th>
                    <th>Checked in at</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bookings as $booking)
                    <tr>
                        <td>{{ $booking->customer_name ?: '—' }}</td>
                        <td>{{ $booking->customer_email ?: '—' }}</td>
                        <td>{{ $booking->booking_date?->format('Y-m-d') }}</td>
                        <td>{{ $booking->displayTime() }}</td>
                        <td><code style="font-size: 0.75rem;">{{ $booking->booking_id }}</code></td>
                        <td>
                            @if ($booking->manifest)
                                {{ $booking->manifest->name }}
                            @else
                                <span title="No matching row in manifests table">{{ $booking->manifest_name ?: '—' }}</span>
                            @endif
                        </td>
                        <td>{{ $booking->duration !== null ? $booking->duration.' min' : '—' }}</td>
                        <td>{{ $booking->state }}</td>
                        <td>
                            @if ($booking->checked_in_at)
                                {{ $booking->checked_in_at->timezone($tz)->format('Y-m-d H:i') }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="actions">
                            <button
                                type="button"
                                class="btn btn-sm js-booking-detail"
                                data-detail-url="{{ route('admin.bookings.show', $booking) }}"
                            >
                                View detail
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" style="text-align: center; color: var(--muted); padding: 2rem;">No check-ins recorded yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($bookings->total() > 0)
        <div class="pagination">
            {{ $bookings->links('vendor.pagination.admin') }}
        </div>
    @endif

    @include('admin.partials.booking_detail_modal')
@endsection
