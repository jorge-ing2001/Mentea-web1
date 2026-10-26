<?php

namespace App\Console\Commands;

use App\Services\WaitlistService;
use Illuminate\Console\Command;

class ExpireWaitlistOffersCommand extends Command
{
    protected $signature = 'mentea:expire-waitlist-offers';

    protected $description = 'Expira ofertas de lista de espera vencidas (60 min) y ofrece al siguiente (FIFO)';

    public function handle(WaitlistService $service): int
    {
        $n = $service->expireOverdueOffers();
        $this->info("Ofertas expiradas: {$n}");
        return self::SUCCESS;
    }
}
