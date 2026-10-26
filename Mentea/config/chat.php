<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Agente conversacional nativo (Mentea AI)
    |--------------------------------------------------------------------------
    |
    | Parámetros del agente de chat embebido en la web. El modelo solo redacta;
    | las reglas duras (consentimiento, crisis, escalado, cupos reales) se
    | aplican en PHP dentro de ChatAgentService. Los textos de crisis, consen-
    | timiento y escalado pueden sobreescribirse en caliente vía SystemSetting
    | (ver claves 'settings_keys'); si no hay override, se usa el valor de aquí.
    |
    */

    // Lee credenciales/modelo de services.openrouter (una sola fuente).
    'model' => env('OPENROUTER_MODEL', 'openai/gpt-4o-mini'),
    'base_url' => env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1'),

    // Cuántos mensajes recientes (user/assistant) se envían al modelo por turno.
    'history_window' => (int) env('CHAT_HISTORY_WINDOW', 12),

    // Tiempo máximo (segundos) de espera a OpenRouter antes de caer al fallback.
    'timeout' => (int) env('CHAT_HTTP_TIMEOUT', 20),

    // Línea de emergencias nacional (fija por protocolo).
    'emergency_line' => '123',

    // Línea de crisis / salud mental configurable.
    'crisis_line' => env('CHAT_CRISIS_LINE', '106'),

    /*
    | Claves de SystemSetting que, si existen, sobreescriben los copys de abajo.
    */
    'settings_keys' => [
        'crisis' => 'chat.crisis_text',
        'consent' => 'chat.consent_text',
        'escalation' => 'chat.escalation_text',
        'crisis_line' => 'chat.crisis_line',
    ],

    /*
    | Copys deterministas en español. El protocolo de crisis arma el texto
    | final insertando emergency_line y crisis_line, pero si existe un override
    | de SystemSetting se usa tal cual (debe incluir las líneas si se desea).
    */
    'crisis_copy' => 'Lamento mucho que estés pasando por esto. Tu vida importa y no estás solo. '
        .'Si estás en peligro inmediato o piensas en hacerte daño, por favor comunícate ahora mismo '
        .'con la línea de emergencias :emergency_line o con la línea de crisis :crisis_line. '
        .'Voy a conectarte de inmediato con una persona de nuestro equipo para acompañarte.',

    'consent_copy' => 'Antes de continuar y agendar tu cita necesitamos tu consentimiento para el '
        .'tratamiento de tus datos personales (Ley 1581 de 2012). Puedes aceptar el consentimiento '
        .'informado desde tu perfil. Sin este consentimiento no puedo recolectar tus datos ni agendar.',

    'escalation_copy' => 'Para ayudarte mejor con esto voy a conectarte con una persona de nuestro '
        .'equipo. No puedo dar orientación clínica ni diagnósticos. Un integrante del equipo de Mentea '
        .'retomará tu caso lo antes posible.',

    'guest_booking_copy' => 'Con gusto te ayudo a agendar. Para hacerlo necesitas iniciar sesión como '
        .'paciente y aceptar el consentimiento informado. Mientras tanto puedo darte información general.',

    'greeting_copy' => '¡Hola! Soy el asistente de Mentea. Puedo darte información sobre nuestros '
        .'servicios de salud mental y ayudarte a agendar una cita. ¿En qué puedo ayudarte hoy?',

    'no_availability_copy' => 'Por el momento no tenemos disponibilidad para ese servicio. Puedo '
        .'ofrecerte otro servicio o anotarte en la lista de espera para avisarte cuando se libere un cupo.',

    /*
    | Catálogo de servicios de negocio mapeado al campo Professional.profession.
    */
    'services' => [
        'Psicología',
        'Psiquiatría',
        'Neuropsicología',
        'Terapia Ocupacional',
        'Terapia Física',
    ],

    /*
    | Heurística de detección de crisis (palabras/frases en español). El código
    | la evalúa ANTES de cualquier otra cosa: si hay match, se detiene el
    | agendamiento y se entrega el texto de crisis.
    */
    'crisis_keywords' => [
        'suicidio', 'suicidarme', 'quitarme la vida', 'quitarme la vída',
        'hacerme daño', 'hacerme dano', 'lastimarme', 'matarme', 'me quiero morir',
        'quiero morir', 'no quiero vivir', 'no quiero seguir viviendo',
        'acabar con todo', 'acabar con mi vida', 'terminar con mi vida',
        'autolesion', 'autolesión', 'me voy a matar', 'quitarme de en medio',
    ],

    /*
    | Heurística de escalado a humano (preguntas clínicas / fuera de alcance /
    | menores o acudientes). El agente no diagnostica ni aconseja.
    */
    'escalation_keywords' => [
        'diagnostico', 'diagnóstico', 'diagnosticar', 'que medicamento', 'qué medicamento',
        'qué me tomo', 'que me tomo', 'dosis', 'receta', 'recetar',
        'mi hijo', 'mi hija', 'menor de edad', 'acudiente', 'soy menor',
        'tengo 12', 'tengo 13', 'tengo 14', 'tengo 15', 'tengo 16', 'tengo 17',
    ],
];
