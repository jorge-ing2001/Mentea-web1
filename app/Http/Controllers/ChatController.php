<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Services\Chat\ChatAgentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Punto de entrada HTTP del agente conversacional nativo de Mentea.
 *
 * Opera bajo el middleware `web` (sesión + CSRF). Resuelve la conversación del
 * usuario autenticado o, si es invitado, de su sesión, delega en
 * ChatAgentService (que aplica todas las reglas duras) y devuelve la respuesta
 * del asistente como JSON. Nunca expone trazas: ante cualquier fallo responde
 * con un mensaje neutro en español.
 */
class ChatController extends Controller
{
    public function store(Request $request, ChatAgentService $agent): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ], [
            'message.required' => 'Escribe un mensaje para continuar.',
            'message.string' => 'El mensaje no es válido.',
            'message.max' => 'El mensaje es demasiado largo.',
        ]);

        $user = Auth::user();
        $conversation = $this->resolveConversation($request, $user?->id);

        try {
            $result = $agent->handle($conversation, $validated['message'], $user);
        } catch (\Throwable $e) {
            Log::error('Fallo en el agente de chat de Mentea.', [
                'conversation_id' => $conversation->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'reply' => 'Lo siento, tuve un inconveniente para procesar tu mensaje. '
                    .'Por favor inténtalo de nuevo en unos instantes.',
                'conversation_id' => $conversation->id,
                'flags' => [
                    'crisis' => false,
                    'escalated' => false,
                    'requires_consent' => false,
                    'requires_login' => false,
                    'error' => true,
                ],
                'slots' => [],
            ], 200);
        }

        return response()->json([
            'reply' => $result['reply'],
            'conversation_id' => $result['conversation_id'],
            'flags' => [
                'crisis' => $result['crisis'],
                'escalated' => $result['escalated'],
                'requires_consent' => $result['requires_consent'],
                'requires_login' => $result['requires_login'] ?? false,
            ],
            'slots' => $result['slots'],
        ]);
    }

    /**
     * Resuelve (o crea) la conversación activa. Para usuarios autenticados se
     * llavea por user_id; para invitados por el id de sesión, de modo que la
     * memoria persiste entre turnos sin requerir login.
     */
    private function resolveConversation(Request $request, ?int $userId): ChatConversation
    {
        if ($userId !== null) {
            return ChatConversation::firstOrCreate(
                ['user_id' => $userId, 'status' => 'active'],
                ['last_activity_at' => now()],
            );
        }

        $sessionId = $request->session()->getId();

        return ChatConversation::firstOrCreate(
            ['session_id' => $sessionId, 'user_id' => null, 'status' => 'active'],
            ['last_activity_at' => now()],
        );
    }
}
