<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;

/** Logging estructurado mínimo para eventos clínicos / operativos */
class MenteaLog
{
    public static function event(string $event, array $context = []): void
    {
        Log::info($event, array_merge([
            'app' => 'mentea',
            'event' => $event,
            'ts' => now()->toIso8601String(),
        ], $context));
    }

    public static function security(string $event, array $context = []): void
    {
        Log::warning($event, array_merge([
            'app' => 'mentea',
            'channel' => 'security',
            'event' => $event,
            'ts' => now()->toIso8601String(),
        ], $context));
    }
}
