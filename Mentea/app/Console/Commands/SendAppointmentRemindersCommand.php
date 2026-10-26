<?php

namespace App\Console\Commands;

use App\Jobs\SendAppointmentReminders;
use Illuminate\Console\Command;

class SendAppointmentRemindersCommand extends Command
{
    protected $signature = 'mentea:send-reminders';

    protected $description = 'Envía recordatorios de citas para el día siguiente';

    public function handle(): int
    {
        $this->info('Enviando recordatorios...');
        SendAppointmentReminders::dispatchSync();
        $this->info('Listo.');
        return self::SUCCESS;
    }
}
