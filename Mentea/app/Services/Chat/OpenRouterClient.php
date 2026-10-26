<?php

namespace App\Services\Chat;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Cliente delgado para OpenRouter usando el cliente HTTP de Laravel
 * (Illuminate\Support\Facades\Http) para que Http::fake() funcione en tests.
 *
 * Degradación grácil: si no hay OPENROUTER_API_KEY configurada, NO se hace
 * ninguna llamada de red; se devuelve null y el orquestador usa su ruta
 * determinista. La app nunca se cae por falta de key o de red.
 */
class OpenRouterClient
{
    private static bool $warnedMissingKey = false;

    public function isConfigured(): bool
    {
        return ! empty(config('services.openrouter.key'));
    }

    /**
     * Envía los mensajes a OpenRouter y devuelve el contenido de texto de la
     * respuesta del asistente, o null si no hay key / falla la red.
     *
     * @param  array<int,array<string,mixed>>  $messages
     */
    public function chat(array $messages): ?string
    {
        if (! $this->isConfigured()) {
            if (! self::$warnedMissingKey) {
                Log::warning('OpenRouter no configurado (OPENROUTER_API_KEY vacío); el agente usa la ruta determinista.');
                self::$warnedMissingKey = true;
            }

            return null;
        }

        $baseUrl = rtrim((string) config('services.openrouter.base_url', 'https://openrouter.ai/api/v1'), '/');
        $model = (string) config('services.openrouter.model', 'openai/gpt-4o-mini');
        $timeout = (int) config('chat.timeout', 20);

        try {
            $response = Http::withToken((string) config('services.openrouter.key'))
                ->timeout($timeout)
                ->acceptJson()
                ->asJson()
                ->post($baseUrl.'/chat/completions', [
                    'model' => $model,
                    'messages' => $messages,
                ]);

            if (! $response->successful()) {
                Log::warning('OpenRouter respondió con error; se usa la ruta determinista.', [
                    'status' => $response->status(),
                ]);

                return null;
            }

            $content = $response->json('choices.0.message.content');

            return is_string($content) && trim($content) !== '' ? trim($content) : null;
        } catch (\Throwable $e) {
            Log::warning('Fallo al llamar a OpenRouter; se usa la ruta determinista.', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Clasifica un mensaje en crisis / no-crisis consultando al modelo. Es una
     * llamada dedicada (prompt de clasificación + mensaje del usuario) que
     * devuelve el contenido crudo del asistente para que el llamador lo parsee,
     * o null si no hay key / falla la red / respuesta vacía. Reusa `chat()`
     * (mismo cliente Illuminate\Http) para mantener una sola ruta HTTP falseable
     * con Http::fake() y la misma degradación grácil (nunca lanza).
     */
    public function classify(string $systemPrompt, string $message): ?string
    {
        return $this->chat([
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $message],
        ]);
    }
}
