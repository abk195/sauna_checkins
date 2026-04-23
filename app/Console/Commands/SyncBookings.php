<?php

namespace App\Console\Commands;

use App\Services\BookingSyncService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class SyncBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync bookings from Periode API into local database';

    public function __construct(
        protected BookingSyncService $bookingSyncService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Booking sync started.');

        $syncWindow = Config::get('periode.sync_window', [
            'days_back' => 7,
            'days_forward' => 7,
        ]);

        $daysBack = (int) ($syncWindow['days_back'] ?? 1);
        $daysForward = (int) ($syncWindow['days_forward'] ?? 7);

        $startDate = Carbon::today()->subDays($daysBack);
        $endDate = Carbon::today()->addDays($daysForward);

        $this->line(sprintf(
            'Date window: %s to %s (back: %d, forward: %d)',
            $startDate->toDateString(),
            $endDate->toDateString(),
            $daysBack,
            $daysForward
        ));

        $startedAt = microtime(true);

        try {
            $this->bookingSyncService->sync();
        } catch (\Throwable $e) {
            Log::error('Booking sync command failed.', [
                'exception' => $e->getMessage(),
            ]);

            $this->error('Booking sync failed: '.$e->getMessage());

            return self::FAILURE;
        }

        $duration = microtime(true) - $startedAt;

        $this->info(sprintf('Booking sync completed in %.2f seconds.', $duration));

        return self::SUCCESS;
    }
}

