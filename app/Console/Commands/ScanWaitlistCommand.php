<?php

namespace App\Console\Commands;

use App\Jobs\ScanWaitlistSlots;
use Illuminate\Console\Command;

class ScanWaitlistCommand extends Command
{
    protected $signature = 'mentea:scan-waitlist {--days=14}';
    protected $description = 'Escanea huecos libres y ofrece a la lista de espera (FIFO)';

    public function handle(): int
    {
        ScanWaitlistSlots::dispatchSync((int) $this->option('days'));
        $this->info('Escaneo de lista de espera ejecutado.');
        return self::SUCCESS;
    }
}
