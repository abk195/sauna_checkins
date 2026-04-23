<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class PeriodeApiService
{
    /**
     * Fetch bookings for a specific manifest and date from the Periode API.
     *
     * @param  string  $manifestId
     * @param  string  $date  Date string in Y-m-d format.
     * @return array
     */
    public function getBookings(string $manifestId, string $date): array
    {
        $baseUrl = rtrim((string) Config::get('periode.base_url'), '/');
        $merchantId = Config::get('periode.merchant_id');
        $apiKey = Config::get('periode.api_key');

        if (empty($baseUrl) || empty($merchantId) || empty($apiKey)) {
            Log::error('Periode API configuration is incomplete.', [
                'base_url' => $baseUrl,
                'merchant_id' => $merchantId,
                'has_api_key' => ! empty($apiKey),
            ]);

            return [];
        }

        $url = sprintf(
            '%s/merchants/%s/getBookings/%s/%s',
            $baseUrl,
            $merchantId,
            $manifestId,
            $date
        );

        try {
            $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'x-api-key' => $apiKey,
                ])
                ->timeout(10)
                ->retry(2, 1000)
                ->get($url);

            if (! $response->successful()) {
                Log::error('Periode API request failed.', [
                    'url' => $url,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [];
            }

            $data = $response->json();

            if (! is_array($data)) {
                Log::error('Periode API returned unexpected payload.', [
                    'url' => $url,
                    'payload' => $data,
                ]);

                return [];
            }

            return $data;
        } catch (Throwable $e) {
            Log::error('Periode API request error.', [
                'url' => $url,
                'exception' => $e->getMessage(),
            ]);

            return [];
        }
    }
}

