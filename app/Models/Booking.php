<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'booking_id',
        'manifest_id',
        'manifest_name',
        'merchant_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'booking_date',
        'booking_time',
        'duration',
        'price',
        'currency',
        'payment_type',
        'state',
        'checked_in',
        'checked_in_at',
        'raw_payload',
        'synced_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'checked_in' => 'boolean',
        'raw_payload' => 'array',
        'booking_date' => 'date',
        'checked_in_at' => 'datetime',
        'synced_at' => 'datetime',
    ];

    /**
     * Manifest row from the local manifests table (matched by Periode manifest_id string).
     */
    public function manifest(): BelongsTo
    {
        return $this->belongsTo(Manifest::class, 'manifest_id', 'manifest_id');
    }

    /**
     * Start datetime for this booking (uses Periode decimal hour in raw_payload when present).
     */
    public function startsAt(): Carbon
    {
        $tz = config('app.timezone');
        $date = $this->booking_date->format('Y-m-d');
        $payload = $this->raw_payload ?? [];
        $t = $payload['time'] ?? null;

        $base = Carbon::parse($date, $tz)->startOfDay();

        if (is_numeric($t)) {
            $tf = (float) $t;
            $h = (int) floor($tf);
            $m = (int) round(($tf - $h) * 60);

            return $base->copy()->setTime($h, min($m, 59), 0);
        }

        if ($this->booking_time) {
            return Carbon::parse($date.' '.$this->formatTimeString($this->booking_time), $tz);
        }

        return $base;
    }

    /**
     * Human-readable time for UI (24h).
     */
    public function displayTime(): string
    {
        return $this->startsAt()->format('H:i');
    }

    /**
     * @param  mixed  $value
     */
    protected function formatTimeString($value): string
    {
        if ($value instanceof Carbon) {
            return $value->format('H:i:s');
        }

        if (is_string($value)) {
            return $value;
        }

        return '00:00:00';
    }
}
