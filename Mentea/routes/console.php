<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

Schedule::command('mentea:send-reminders')->everyFifteenMinutes();

Schedule::command('mentea:expire-waitlist-offers')->everyMinute();

Schedule::command('mentea:scan-waitlist')->hourly();
Schedule::job(new \App\Jobs\SendSurveyReminders)->dailyAt('10:00');
