<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Manifest;
use App\Models\Sauna;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class SaunaActiveSessionResolver
{
    /**
     * Minutes before a manifest's session start when the iPad should switch to it.
     */
    public const SWITCH_LEAD_MINUTES = 20;

    /**
     * Resolve the active manifest + bookings for a sauna at the given moment.
     *
     * Switching rule:
     *  - The active session is the manifest with the latest `session_start` such that
     *    `session_start - 20min <= now`.
     *  - Before the first switch threshold of the day -> show the first manifest of the day.
     *  - After the last manifest -> stays on the last manifest.
     *
     * @return array{
     *     sauna: Sauna,
     *     active_manifest: Manifest|null,
     *     active_session_start: CarbonInterface|null,
     *     bookings: Collection<int, Booking>,
     *     all_sessions: Collection<int, array{manifest: Manifest, session_start: CarbonInterface}>,
     * }
     */
    public function resolve(Sauna $sauna, ?CarbonInterface $now = null): array
    {
        $tz = config('app.timezone');
        $now = ($now ?? Carbon::now($tz))->copy()->setTimezone($tz);
        $today = $now->copy()->startOfDay()->toDateString();

        $manifests = $sauna->manifests()->get()->keyBy('manifest_id');

        $todayBookings = collect();
        if ($manifests->isNotEmpty()) {
            $todayBookings = Booking::query()
                ->whereDate('booking_date', $today)
                ->whereNotIn('state', ['cancelled'])
                ->whereIn('manifest_id', $manifests->keys())
                ->get();
        }

        $sessions = $todayBookings
            ->groupBy('manifest_id')
            ->map(function (Collection $group) use ($manifests) {
                $first = $group->first();
                /** @var Manifest|null $manifest */
                $manifest = $manifests->get($first->manifest_id);
                if (! $manifest) {
                    return null;
                }

                $start = $group
                    ->map(fn (Booking $b) => $b->startsAt())
                    ->sortBy(fn (CarbonInterface $c) => $c->getTimestamp())
                    ->first();

                return [
                    'manifest' => $manifest,
                    'session_start' => $start,
                ];
            })
            ->filter()
            ->sortBy(fn (array $row) => $row['session_start']->getTimestamp())
            ->values();

        $active = null;
        foreach ($sessions as $session) {
            $threshold = $session['session_start']->copy()->subMinutes(self::SWITCH_LEAD_MINUTES);
            if ($threshold->lessThanOrEqualTo($now)) {
                $active = $session;
            } else {
                break;
            }
        }

        if ($active === null && $sessions->isNotEmpty()) {
            $active = $sessions->first();
        }

        $activeManifest = $active['manifest'] ?? null;
        $activeBookings = $activeManifest
            ? $todayBookings
                ->where('manifest_id', $activeManifest->manifest_id)
                ->sortBy(fn (Booking $b) => $b->startsAt()->getTimestamp())
                ->values()
            : collect();

        return [
            'sauna' => $sauna,
            'active_manifest' => $activeManifest,
            'active_session_start' => $active['session_start'] ?? null,
            'bookings' => $activeBookings,
            'all_sessions' => $sessions,
        ];
    }
}
