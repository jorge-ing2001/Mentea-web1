# Agente conversacional nativo en PHP (Mentea AI)

> Documento de diseño / contrato de arquitectura. Lo escribe FEAT-001 y lo
> implementan FEAT-002 (backend: config, migraciones, servicios, cliente
> OpenRouter, controlador) y FEAT-003 (widget Blade + Alpine y rutas).
>
> Idioma de cara al usuario: **español** (locale `es`). Todo texto visible debe
> estar en español, neutro y respetuoso de la privacidad.

## 1. Objetivo y alcance

Agente conversacional embebido en la propia web Laravel de Mentea (hospital de
salud mental, Colombia). Agenda citas y responde consultas reutilizando los
servicios ya existentes. **No** cambia de plataforma ni reimplementa la lógica
de agenda: orquesta `AvailabilityService`, `AppointmentService` y
`WaitlistService`. OpenRouter se invoca desde PHP con el cliente HTTP de
Laravel (una sola API para hablar con cualquier modelo, modelo configurable).

El agente ofrece de **2 a 4** opciones reales de cupo, hace una reserva
temporal (hold), confirma tras re-validar, reprograma, cancela y ofrece lista
de espera cuando no hay cupo.

### Principio rector: el código manda, el modelo solo redacta

El modelo (LLM) **nunca** decide reglas de negocio ni inventa datos. El modelo
únicamente **redacta** (phrasing) a partir de datos y decisiones que el código
PHP ya calculó y validó. Toda regla dura se aplica en PHP, antes y después de
llamar al modelo. Si el modelo propusiera algo que viola una regla, el código
lo descarta: la verdad son los datos de la agenda y los chequeos de los
servicios.

## 2. Componentes

```
Navegador (widget Blade + Alpine)
        │  POST /chat  (web + session + CSRF; auth opcional)
        ▼
ChatController
        │  valida request, resuelve conversación/memoria
        ▼
ChatAgentService  (orquestador)
   ├── aplica reglas duras (consentimiento, crisis, escalado, alcance)
   ├── Tool layer  ──► AvailabilityService / AppointmentService / WaitlistService
   └── OpenRouterClient (Illuminate\Support\Facades\Http) ──► OpenRouter
        │  (solo redacta; degradación grácil si no hay API key)
        ▼
chat_conversations + chat_messages  (memoria persistente)
```

### 2.1 `ChatController`

- Ruta `POST /chat` con nombre `chat.message`, bajo el grupo middleware `web`
  (sesión + CSRF). **No** requiere `auth`: los invitados pueden conversar y
  pedir información; agendar sí exige paciente autenticado con consentimiento
  (ver §4 y §8). Ruta de salud/estado opcional `GET /chat` para rehidratar la
  conversación actual.
- Responsabilidades: validar el payload (`message` string, límite de longitud),
  resolver la conversación (por `user_id` si hay sesión autenticada, o por
  `session()->getId()` para invitados), delegar en `ChatAgentService` y
  devolver JSON (`reply`, `conversation_id`, metadatos como `escalated`,
  `crisis`, `requires_consent`, `slots` cuando aplique).
- No contiene lógica de negocio: delega todo en el servicio.

### 2.2 `ChatAgentService` (orquestador)

Flujo por turno:

1. Persistir el mensaje entrante del usuario en `chat_messages`.
2. **Reglas duras previas** (en orden; cualquiera puede cortar el turno):
   - Detección de **crisis** → responde texto de crisis configurable + línea
     123 + línea de crisis configurable, marca escalado, **detiene** el
     agendamiento. No se llama al modelo para "mejorar" este texto.
   - Detección de contenido que exige **escalado a humano** (preguntas
     clínicas / diagnóstico, menores o acudientes, fuera de alcance, casos
     dudosos) → mensaje neutro de escalado.
   - **Puerta de consentimiento** (Ley 1581/2012): antes de recolectar datos
     personales o de agendar, exige consentimiento. Sin consentimiento no
     continúa el flujo de datos/reserva.
3. Construir el contexto: *system prompt* (reglas + catálogo + tono) + ventana
   de historial reciente (§5) + mensaje actual + definiciones de herramientas.
4. Llamar a `OpenRouterClient`. El modelo puede solicitar herramientas; el
   servicio ejecuta la herramienta correspondiente (datos reales) y vuelve a
   pedir al modelo que **redacte** la respuesta con esos datos.
5. **Re-verificación antes de confirmar**: cualquier reserva/confirmación se
   revalida contra `AvailabilityService` / `AppointmentService` en PHP justo
   antes de persistir. Nunca se confirma un cupo que el modelo "recuerde".
6. Persistir la respuesta del asistente en `chat_messages` y devolverla.

Si no hay API key o la llamada a OpenRouter falla, se usa una **ruta de
respuesta determinista** (fallback) que sigue aplicando las reglas duras y
puede ofrecer cupos reales / escalar, con copy fijo en español (§6).

### 2.3 Capa de herramientas (tool layer)

Adaptadores finos sobre los servicios existentes; **no** reimplementan lógica.
Firmas de referencia (ya existentes en el código):

- `AvailabilityService::getAvailableSlots(Professional, Carbon): Collection<'H:i'>`
- `AvailabilityService::getAvailableDates(Professional, int=45): Collection<'Y-m-d'>`
- `AvailabilityService::isSlotAvailable(Professional, Carbon, string)`
- `AvailabilityService::isSlotFreeOfAppointments(Professional, Carbon, string)`
- `AppointmentService::book(User, Professional, string $date, string $time, Modality, ?reason, ?orderDoc, ?authDoc, bool $skipAvailabilityCheck)`
  - ya aplica: bloqueo de agendamiento, `hasInformedConsent()`, disponibilidad,
    doble-reserva del paciente y transacción DB.
- `AppointmentService::reschedule(...)`, `cancelByPatient(...)`, `confirm(...)`,
  `assertPatientHasNoConflict(...)`
- `WaitlistService` (`HOLD_MINUTES = 60`): `onSlotFreed`, `offerSlot`,
  `confirmOffer(WaitlistEntry, AppointmentService)`, y alta de entrada.

Herramientas que expondrá el agente (nombres orientativos, FEAT-002 fija los
definitivos):

| Herramienta           | Delegación                                   | Devuelve |
|-----------------------|----------------------------------------------|----------|
| `list_services`       | catálogo (§7)                                | servicios disponibles |
| `find_availability`   | `getAvailableDates` + `getAvailableSlots`    | **2 a 4** cupos reales |
| `hold_slot`           | reserva temporal (hold de 60 min)            | hold + expiración |
| `confirm_booking`     | re-verifica y `AppointmentService::book`     | cita confirmada |
| `reschedule_booking`  | `AppointmentService::reschedule`             | cita reprogramada |
| `cancel_booking`      | `AppointmentService::cancelByPatient`        | cita cancelada |
| `join_waitlist`       | `WaitlistService` (alta de `WaitlistEntry`)  | entrada en lista |

Regla transversal de todas las herramientas de cupo: **solo** se ofrecen slots
devueltos por `AvailabilityService`; nunca se inventan horarios. Antes de
confirmar se vuelve a validar el cupo (holds de lista de espera se respetan vía
`isSlotFreeOfAppointments` / `skipAvailabilityCheck`).

### 2.4 `OpenRouterClient`

- Usa `Illuminate\Support\Facades\Http` (para que `Http::fake()` funcione en
  tests; **sin** red real en CI).
- Lee credenciales y modelo de config (§6). Base URL configurable.
- **Degradación grácil**: si `OPENROUTER_API_KEY` está ausente, el cliente
  reporta "no configurado" y el orquestador usa la ruta determinista; la app
  nunca se cae por falta de key.
- Timeouts cortos y manejo de error: cualquier fallo de red cae al fallback.

## 3. Frontera de reglas duras (code enforces, model phrases)

Estas reglas se implementan y verifican en PHP. El modelo solo cambia la
redacción, nunca la decisión:

1. **Consentimiento primero (Ley 1581/2012).** Antes de recolectar datos
   personales o agendar, el agente exige consentimiento de tratamiento de
   datos. Sin consentimiento, el flujo de datos/reserva no continúa. En paciente
   autenticado se cruza con `User::hasInformedConsent()`
   (`consent_informed_at`); `AppointmentService::book()` ya rechaza si falta.
2. **Nunca inventar horarios.** Solo se ofrece disponibilidad real
   (`AvailabilityService`) y se **re-verifica** inmediatamente antes de
   confirmar. 2 a 4 opciones por turno.
3. **Protocolo de crisis.** Ante señales de crisis/riesgo: detener el
   agendamiento, entregar el **texto de crisis configurable** con la línea de
   emergencias **123** y una **línea de crisis configurable**, y **escalar a una
   persona** de inmediato. El agente solo ejecuta ese texto (configurable); no
   improvisa.
4. **Escalado a humano** en casos dudosos, preguntas clínicas (el agente **no
   diagnostica ni aconseja**), menores/acudientes, o mensajes fuera de alcance.
5. **Mensajes/recordatorios neutros** por privacidad (sin detalles clínicos
   sensibles en texto visible/notificaciones).

Orden de evaluación por turno: **crisis → escalado → consentimiento → cupo /
redacción**. La regla más protectora corta antes.

## 4. Modelo de datos de memoria

Dos tablas nuevas (FEAT-002 crea las migraciones):

### `chat_conversations`
- `id`
- `user_id` nullable (FK a `users`) — conversación de paciente autenticado.
- `session_id` nullable (string) — conversación de invitado (`session()->getId()`).
- `status` / flags: p.ej. `escalated_at`, `crisis_flagged_at` (nullables) para
  auditoría del protocolo.
- `consent_captured_at` nullable — consentimiento capturado dentro del chat
  (complementa `users.consent_informed_at`).
- timestamps.
- Índices por `user_id` y por `session_id`.

### `chat_messages`
- `id`, `chat_conversation_id` (FK, cascade on delete).
- `role` enum/string: `user` | `assistant` | `system` | `tool`.
- `content` text.
- `meta` json nullable (herramienta usada, slots ofrecidos, flags de crisis/
  escalado, modelo utilizado, etc.).
- timestamps.
- Índice por `chat_conversation_id` + `created_at`.

**Ventana de historial**: en cada turno se envía al modelo el *system prompt* +
los últimos N mensajes (`user`/`assistant`) de la conversación (N configurable,
p.ej. 10-20) + el mensaje nuevo. El historial se **persiste** siempre; solo la
ventana reciente se manda al modelo para acotar tokens.

Resolución de conversación: si hay usuario autenticado → por `user_id`; si no →
por `session_id`. Al iniciar sesión se puede vincular la conversación de
invitado al usuario (opcional, FEAT-002 decide).

## 5. Configuración y credenciales

### `config/services.php` (bloque nuevo `openrouter`)
```php
'openrouter' => [
    'key'      => env('OPENROUTER_API_KEY'),
    'base_url' => env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1'),
    'model'    => env('OPENROUTER_MODEL', 'openai/gpt-4o-mini'),
],
```

### `config/chat.php` (nuevo) — parámetros del agente
- `model`, `base_url` (pueden leer de `services.openrouter`).
- `history_window` (N mensajes recientes).
- `system_prompt` (reglas + tono en español).
- `consent_copy` (texto de la puerta de consentimiento).
- `crisis_copy` (texto de crisis con línea 123 + línea de crisis).
- `crisis_line` (línea de crisis configurable).
- `escalation_copy` (texto de escalado a humano).
- `enabled` / flags de degradación.

> Alternativa admitida: añadir estas claves en `config/mentea.php`
> (que ya existe con `meet_mode`, etc.). FEAT-002 elige; el contrato es que
> exista una fuente de config única para prompt + copys + modelo.

### Override por `SystemSetting`
El texto de **crisis** (y opcionalmente consentimiento/escalado) debe poder
sobreescribirse en caliente vía `SystemSetting::getValue(key, default)` /
`SystemSetting::setValue(...)` (store cacheado de la app). El orquestador lee
primero `SystemSetting`, luego cae a `config/chat.php`.

### Degradación grácil (sin API key / sin red)
- Si falta `OPENROUTER_API_KEY`: el agente funciona en modo determinista.
  Sigue aplicando reglas duras, puede listar servicios, ofrecer cupos reales y
  escalar/entregar crisis, usando los copys fijos en español. **No** se cae.
- En tests: `Http::fake()` simula OpenRouter; **ninguna** prueba requiere red.
- `.env.example` documentará `OPENROUTER_API_KEY`, `OPENROUTER_MODEL`,
  `OPENROUTER_BASE_URL`, `CHAT_*` (FEAT-002).

## 6. Catálogo de servicios (5 de negocio → `Professional.profession`)

Los 5 servicios del plan de negocio se mapean al campo `profession` del modelo
`Professional`:

| Servicio de negocio    | `profession`         | ¿Sembrado? |
|------------------------|----------------------|------------|
| Psicología             | `Psicología`         | Sí |
| Psiquiatría            | `Psiquiatría`        | Sí |
| Neuropsicología        | `Neuropsicología`    | Sí |
| Terapia ocupacional    | `Terapia Ocupacional`| **No** |
| Terapia física         | `Terapia Física`     | **No** |

El seeder actual solo crea profesionales de **Psicología / Psiquiatría /
Neuropsicología**. Para Terapia Ocupacional y Terapia Física no hay
profesionales disponibles: el agente debe **informarlo con gracia** ("por el
momento no tenemos disponibilidad para ese servicio") y ofrecer alternativas
(otro servicio, lista de espera), **sin inventar** profesionales ni cupos.

Nota: la lista de espera (`WaitlistService::onSlotFreed`) hace match por
`professional->specialty` exacto (granular, p.ej. "Psicología Clínica"),
mientras que el catálogo del chat agrupa por `profession` (amplio). Las
herramientas deben traducir entre ambos niveles con cuidado.

## 7. Widget (Blade + Alpine)

- Componente Blade + Alpine incluido en `resources/views/layouts/app.blade.php`
  (layout autenticado). El layout ya expone `<meta name="csrf-token">` y
  `@vite([...])`; Alpine ya es dependencia. El widget envía el token CSRF en el
  `POST /chat`.
- UI: burbuja flotante que abre un panel de chat; lista de mensajes, input,
  estado de "escribiendo", y render de los **2-4 cupos** como botones
  seleccionables cuando el backend los devuelve.
- **Comportamiento de invitado**: un invitado puede conversar y pedir
  información general; **agendar** requiere paciente autenticado con
  consentimiento. Si un invitado intenta agendar, el agente degrada con
  gracia (invita a iniciar sesión / registrarse), **nunca** rompe la página.
  El widget debe funcionar sin usuario y sin API key.
- Rutas de paciente: el grupo real vive tras
  `Route::middleware('role:patient')->prefix('paciente')->name('patient.')`.
  El `POST /chat` en sí va en el grupo `web` general (sin `role:patient`) para
  permitir invitados; la autorización de agendar se decide dentro del servicio.

## 8. Pruebas (contrato para FEAT-002/003)

- `Http::fake()` para OpenRouter; sin red real.
- Casos mínimos: puerta de consentimiento bloquea reserva sin consentimiento;
  crisis devuelve copy configurable + 123 + línea de crisis y marca escalado;
  disponibilidad devuelve 2-4 cupos reales y nunca inventa; confirmación
  re-verifica el cupo; servicio sin profesionales (terapia ocupacional/física)
  responde "sin disponibilidad" sin inventar; invitado puede conversar pero no
  agendar; funcionamiento sin `OPENROUTER_API_KEY` (fallback determinista).
- Todo debe mantener verde `.github/workflows/ci.yml` (PHP 8.3, sqlite,
  `QUEUE_CONNECTION=sync`, `MAIL_MAILER=array`). Estilo con Laravel Pint.

## 9. Resumen de reglas no negociables

1. Consentimiento antes de datos personales / reserva (Ley 1581/2012).
2. Solo cupos reales de la agenda; re-verificar antes de confirmar; 2-4 opciones.
3. Crisis → texto configurable + 123 + línea de crisis + escalado; detener agenda.
4. Escalado a humano en casos clínicos, dudosos, menores/acudientes o fuera de alcance; el agente no diagnostica ni aconseja.
5. Mensajes neutros por privacidad.
6. El código aplica las reglas; el modelo solo redacta.
7. Sin API key / sin red: degradación grácil determinista, nunca se cae.
