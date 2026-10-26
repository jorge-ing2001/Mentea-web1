<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'status',
        'last_activity_at',
        'consent_captured_at',
        'crisis_flagged_at',
        'escalated_at',
    ];

    protected function casts(): array
    {
        return [
            'last_activity_at' => 'datetime',
            'consent_captured_at' => 'datetime',
            'crisis_flagged_at' => 'datetime',
            'escalated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    /**
     * Últimos N mensajes user/assistant en orden cronológico para la ventana
     * de memoria que se envía al modelo.
     */
    public function recentHistory(int $window): array
    {
        return $this->messages()
            ->whereIn('role', ['user', 'assistant'])
            ->latest('id')
            ->limit($window)
            ->get()
            ->sortBy('id')
            ->map(fn (ChatMessage $m) => [
                'role' => $m->role,
                'content' => $m->content,
            ])
            ->values()
            ->all();
    }
}
