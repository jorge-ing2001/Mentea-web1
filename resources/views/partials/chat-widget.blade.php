{{--
    Widget flotante del agente conversacional de Mentea.

    Burbuja flotante + panel deslizable. Toda la lógica va inline con Alpine
    (x-data) para no depender del build de Vite. Postea a la ruta `chat.send`
    con el token CSRF del <meta name="csrf-token">. Muestra estado de carga y,
    ante cualquier error, un mensaje neutro en español (nunca una traza).
--}}
<div
    x-data="menteaChat()"
    x-cloak
    class="mentea-chat-widget"
    aria-live="polite"
>
    {{-- Panel de conversación --}}
    <div
        x-show="open"
        x-transition.opacity.duration.200ms
        class="mentea-chat-panel"
        role="dialog"
        aria-label="Asistente de Mentea"
    >
        <div class="mentea-chat-header">
            <div class="mentea-chat-header-title">
                <span class="mentea-chat-dot" aria-hidden="true"></span>
                <span>Asistente de Mentea</span>
            </div>
            <button type="button" class="mentea-chat-close" @click="open = false" aria-label="Cerrar chat">&times;</button>
        </div>

        <div class="mentea-chat-messages" x-ref="messages">
            <template x-for="(msg, idx) in messages" :key="idx">
                <div :class="msg.role === 'user' ? 'mentea-chat-msg mentea-chat-msg-user' : 'mentea-chat-msg mentea-chat-msg-bot'">
                    <span x-text="msg.content"></span>
                </div>
            </template>
            <div x-show="loading" class="mentea-chat-msg mentea-chat-msg-bot mentea-chat-typing">
                <span class="mentea-chat-typing-dot"></span>
                <span class="mentea-chat-typing-dot"></span>
                <span class="mentea-chat-typing-dot"></span>
            </div>
        </div>

        <form class="mentea-chat-input" @submit.prevent="send()">
            <input
                type="text"
                x-model="draft"
                :disabled="loading"
                maxlength="2000"
                placeholder="Escribe tu mensaje…"
                aria-label="Mensaje"
                autocomplete="off"
            >
            <button type="submit" :disabled="loading || draft.trim() === ''" aria-label="Enviar">Enviar</button>
        </form>
    </div>

    {{-- Burbuja flotante --}}
    <button
        type="button"
        class="mentea-chat-bubble"
        @click="toggle()"
        :aria-expanded="open.toString()"
        aria-label="Abrir asistente de Mentea"
    >
        <span x-show="!open" aria-hidden="true">💬</span>
        <span x-show="open" aria-hidden="true">&times;</span>
    </button>
</div>

<script>
function menteaChat() {
    return {
        open: false,
        loading: false,
        draft: '',
        greeted: false,
        messages: [],
        toggle() {
            this.open = !this.open;
            if (this.open && !this.greeted) {
                this.greeted = true;
                this.messages.push({
                    role: 'assistant',
                    content: '¡Hola! Soy el asistente de Mentea. Puedo darte información sobre nuestros servicios de salud mental y ayudarte a agendar una cita. ¿En qué puedo ayudarte hoy?'
                });
                this.scrollDown();
            }
        },
        scrollDown() {
            this.$nextTick(() => {
                const box = this.$refs.messages;
                if (box) { box.scrollTop = box.scrollHeight; }
            });
        },
        async send() {
            const text = this.draft.trim();
            if (text === '' || this.loading) { return; }

            this.messages.push({ role: 'user', content: text });
            this.draft = '';
            this.loading = true;
            this.scrollDown();

            const token = document.querySelector('meta[name="csrf-token"]');
            try {
                const res = await fetch('{{ route('chat.send') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token ? token.getAttribute('content') : '',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ message: text }),
                });

                if (!res.ok) { throw new Error('http ' + res.status); }

                const data = await res.json();
                this.messages.push({
                    role: 'assistant',
                    content: (data && data.reply) ? data.reply : this.fallback(),
                });

                // Separa "debe iniciar sesión" de "debe aceptar consentimiento"
                // para mensajear al invitado correctamente.
                const flags = (data && data.flags) ? data.flags : {};
                if (flags.requires_login) {
                    this.messages.push({
                        role: 'assistant',
                        content: 'Puedes iniciar sesión o registrarte en {{ route('login') }} para agendar tu cita.',
                    });
                } else if (flags.requires_consent) {
                    this.messages.push({
                        role: 'assistant',
                        content: 'Recuerda aceptar el consentimiento informado desde tu perfil antes de agendar.',
                    });
                }
            } catch (e) {
                this.messages.push({ role: 'assistant', content: this.fallback() });
            } finally {
                this.loading = false;
                this.scrollDown();
            }
        },
        fallback() {
            return 'Lo siento, no pude procesar tu mensaje en este momento. Por favor inténtalo de nuevo en unos instantes.';
        },
    };
}
</script>
