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
        'suicidio', 'suicidarme', 'suicidarse', 'quitarme la vida', 'quitarme la vída',
        'hacerme daño', 'hacerme dano', 'lastimarme', 'matarme', 'me quiero morir',
        'quiero morir', 'no quiero vivir', 'no quiero seguir viviendo',
        'acabar con todo', 'acabar con mi vida', 'terminar con mi vida',
        'autolesion', 'autolesión', 'cortarme', 'me voy a matar', 'quitarme de en medio',
        'me quiero desaparecer', 'quiero desaparecer', 'dejar de existir',
        'no aguanto mas', 'ya no aguanto', 'no puedo mas', 'ya no puedo mas',
        'no vale la pena vivir', 'nada tiene sentido', 'no le encuentro sentido',
        'estoy mejor muerto', 'estarian mejor sin mi', 'sin mi estarian mejor',
    ],

    /*
    | Capa de REFUERZO de la detección de crisis: expresiones regulares sobre
    | el texto normalizado (minúsculas + sin tildes). Captura PARÁFRASIS que la
    | lista de palabras exacta no cubre ("ya no le veo sentido a nada",
    | "pienso en acabar con todo esto", "quisiera no despertar"). Esta capa SOLO
    | puede ELEVAR la señal de crisis: nunca anula un match de 'crisis_keywords'.
    | Se evalúa con detección de negación para reducir falsos positivos
    | ("no quiero hacerme daño" NO debe disparar).
    */
    'crisis_patterns' => [
        // Ideación suicida parafraseada.
        '/\b(quiero|quisiera|deseo|pienso en|ganas de|voy a)\b.{0,25}\b(morir(me)?|desaparecer|no (despertar|existir|estar)|dejar de (existir|vivir|ser))\b/u',
        '/\b(no|ya no)\b.{0,15}\b(quiero|deseo|vale la pena)\b.{0,15}\b(vivir|seguir|existir|estar aqui)\b/u',
        // Desesperanza / sin salida.
        '/\b(ya )?no (aguanto|soporto|resisto|puedo)( mas| con esto| con todo| seguir)?\b/u',
        '/\b(no (hay|veo|encuentro)|sin)\b.{0,20}\b(salida|solucion|sentido|esperanza|futuro)\b/u',
        '/\bnada (tiene|me da) sentido\b/u',
        // Autolesión / planes de daño.
        '/\b(hacerme|causarme|provocarme)\b.{0,15}\b(dano|daño|mal)\b/u',
        '/\b(acabar|terminar)\b.{0,15}\b(con (todo|mi vida|esto|mi existencia))\b/u',
        '/\b(cortar(me)?|lastimar(me)?|golpear(me)?)\b.{0,15}\b(para sentir|otra vez|de nuevo|cuando)\b/u',
        // El mundo / los demás estarían mejor sin la persona.
        '/\b(estarian|estarias|estaria todo|todo estaria)\b.{0,15}\bmejor (sin mi|si no (estuviera|existiera))\b/u',
    ],

    /*
    | Marcadores de NEGACIÓN que invalidan una coincidencia de patrón (no de
    | palabra clave explícita). Si el texto contiene una negación directa de la
    | ideación ("no quiero morir", "jamas pensaria en hacerme daño") el patrón
    | NO dispara. La lista 'crisis_keywords' no se ve afectada por esto.
    */
    'crisis_pattern_negations' => [
        'no quiero morir', 'no quiero hacerme dano', 'no quiero hacerme daño',
        'no pienso en', 'jamas pensaria', 'nunca pensaria', 'no me quiero morir',
        'no tengo pensamientos', 'no tengo ideas de',
    ],

    /*
    |--------------------------------------------------------------------------
    | Capa 3: clasificador de crisis asistido por el modelo (ESCALATE-ONLY)
    |--------------------------------------------------------------------------
    |
    | Capa OPCIONAL que SOLO puede ELEVAR la señal de crisis cuando las dos
    | capas deterministas (palabras clave + regex de paráfrasis) NO dispararon.
    | Nunca puede bajar un hit determinista: ChatAgentService::detectsCrisis
    | hace short-circuit y ni siquiera consulta al modelo si una capa
    | determinista ya marcó crisis. Es fail-safe por defecto: si se desactiva,
    | no hay OPENROUTER_API_KEY, falla la red o la respuesta es inparseable, el
    | resultado determinista gobierna y la app nunca se cae.
    |
    | Toggle auditable: CHAT_CRISIS_CLASSIFIER=false lo desactiva por completo
    | (las capas deterministas siguen funcionando sin key ni red).
    */
    'crisis_classifier_enabled' => filter_var(env('CHAT_CRISIS_CLASSIFIER', true), FILTER_VALIDATE_BOOLEAN),

    'crisis_classifier_prompt' => 'Eres un clasificador de seguridad para un hospital de salud mental en '
        .'Colombia. Analiza el ÚLTIMO mensaje del usuario y determina si expresa una crisis de salud mental: '
        .'ideación suicida, autolesión, deseo de morir o desaparecer, desesperanza severa o riesgo inminente '
        .'de daño a sí mismo. Interpreta paráfrasis, jerga y lenguaje indirecto en español. Responde '
        .'EXCLUSIVAMENTE con un JSON de una línea y nada más: {"crisis": true} si detectas crisis o riesgo, '
        .'o {"crisis": false} si no. Ante la duda razonable, prefiere {"crisis": true}. No expliques ni '
        .'añadas texto fuera del JSON.',

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
