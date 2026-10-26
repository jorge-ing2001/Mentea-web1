<?php

namespace App\Jobs;

use App\Enums\WaitlistStatus;
use App\Models\Professional;
use App\Models\WaitlistEntry;
use App\Services\AvailabilityService;
use App\Services\WaitlistService;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

/**
 * Escanea huecos libres de los próximos N días y ofrece a lista de espera FIFO.
 */
class ScanWaitlistSlots implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $daysAhead = 14) {}

    public function handle(AvailabilityService $availability, WaitlistService $waitlist): void
    {
        $waiting = WaitlistEntry::query()
            ->where('status', WaitlistStatus::Waiting->value)
            ->count();

        if ($waiting === 0) {
            return;
        }

        $offered = 0;
        $professionals = Professional::query()->where('is_available', true)->get();

        foreach ($professionals as $professional) {
            for ($d = 0; $d <= $this->daysAhead; $d++) {
                $date = Carbon::today()->addDays($d);
                $slots = $availability->getAvailableSlots($professional, $date);
                foreach ($slots as $time) {
                    $entry = $waitlist->onSlotFreed($professional, $date->toDateString(), $time, null);
                    if ($entry) {
                        $offered++;
                        // Un offer por profesional/día para no saturar
                        break 2;
                    }
                }
            }
        }

        Log::info("Waitlist scan: ofertas generadas={$offered}");
    }
}
