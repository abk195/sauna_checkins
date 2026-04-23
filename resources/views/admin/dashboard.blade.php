@extends('layouts.admin')

@section('title', 'Dashboard')

@section('heading', 'Dashboard')

@section('content')
    <p style="color: var(--muted); margin: 0 0 1.25rem; font-size: 0.95rem;">
        Overview for <strong style="color: var(--text);">{{ $todayLabel }}</strong> ({{ config('app.timezone') }}) and all-time totals.
    </p>

    <div class="stat-grid">
        <article class="stat-card">
            <h3 class="stat-card__label">Total bookings</h3>
            <p class="stat-card__value">{{ number_format($stats['bookings_total']) }}</p>
            <p class="stat-card__hint">All records in database</p>
        </article>
        <article class="stat-card">
            <h3 class="stat-card__label">Checked in</h3>
            <p class="stat-card__value stat-card__value--success">{{ number_format($stats['checked_in_total']) }}</p>
            <p class="stat-card__hint">All time</p>
        </article>
        <article class="stat-card">
            <h3 class="stat-card__label">Pending check-in</h3>
            <p class="stat-card__value stat-card__value--accent">{{ number_format($stats['pending_checkin']) }}</p>
            <p class="stat-card__hint">Not cancelled</p>
        </article>
        <article class="stat-card">
            <h3 class="stat-card__label">Cancelled</h3>
            <p class="stat-card__value stat-card__value--muted">{{ number_format($stats['cancelled']) }}</p>
            <p class="stat-card__hint">All time</p>
        </article>
        <article class="stat-card">
            <h3 class="stat-card__label">Today’s bookings</h3>
            <p class="stat-card__value">{{ number_format($stats['today_bookings']) }}</p>
            <p class="stat-card__hint">Excluding cancelled</p>
        </article>
        <article class="stat-card">
            <h3 class="stat-card__label">Checked in today</h3>
            <p class="stat-card__value stat-card__value--success">{{ number_format($stats['today_checked_in']) }}</p>
            <p class="stat-card__hint">For today’s date</p>
        </article>
        <article class="stat-card stat-card--wide">
            <h3 class="stat-card__label">Manifests</h3>
            <p class="stat-card__value">{{ number_format($stats['manifests_count']) }}</p>
            <p class="stat-card__hint">Used by booking sync — <a href="{{ route('admin.manifests.index') }}">Manage manifests</a></p>
        </article>
    </div>
@endsection
