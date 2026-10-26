# Activar y probar el agente con OpenRouter (API key)

Esta guia muestra como cargar tu API key de OpenRouter para activar la
redaccion con IA y la capa 3 de deteccion de crisis, y como comprobar que
quedo funcional.

> IMPORTANTE (seguridad): la API key va SOLO en tu archivo `.env` local, que
> esta en `.gitignore` y **nunca** se sube a GitHub. No la pongas en el codigo,
> ni en `.env.example`, ni en ningun commit. Si una key llega a aparecer en el
> historial de git, hay que considerarla comprometida y revocarla en OpenRouter.

---

## 1. Obtener la API key

1. Entra a [openrouter.ai/keys](https://openrouter.ai/keys) e inicia sesion.
2. Crea una API key y copiala (tiene el formato `sk-or-v1-...`).

## 2. Cargarla en tu `.env` local

En la raiz del proyecto Laravel (`Mentea/`), abre el archivo `.env` (si no
existe, copialo desde la plantilla) y pega tu key:

```bash
# si aun no tienes .env:
cp .env.example .env
php artisan key:generate   # solo si APP_KEY esta vacio
```

Edita `.env` y completa:

```env
OPENROUTER_API_KEY=sk-or-v1-TU-CLAVE-AQUI
OPENROUTER_MODEL=openai/gpt-4o-mini
OPENROUTER_BASE_URL=https://openrouter.ai/api/v1
CHAT_CRISIS_CLASSIFIER=true
```

Aplica la configuracion:

```bash
php artisan config:clear
```

## 3. Comprobar que la key quedo cargada (sin exponerla)

```bash
php artisan tinker --execute="echo config('services.openrouter.key') ? 'OPENROUTER OK (configurada)' : 'FALTA la key';"
```

Debe imprimir `OPENROUTER OK (configurada)`. Este comando no muestra la clave,
solo confirma que esta presente.

## 4. Probar que el agente responde con el modelo

Levanta la app y el frontend:

```bash
php artisan migrate      # crea chat_conversations y chat_messages (primera vez)
npm install && npm run build
php artisan serve
```

Abre la web, inicia sesion como paciente (con consentimiento aceptado) y usa el
widget de chat flotante. Con la key cargada:

- Las respuestas se redactan con el modelo (mas naturales), pero SIEMPRE sobre
  los datos ya verificados en PHP (el codigo manda; el modelo solo redacta).
- Se activa la capa 3 de crisis (clasificador asistido por el modelo), que solo
  puede **elevar** la senal de crisis, nunca bajarla.

### Prueba rapida por consola (opcional)

```bash
php artisan tinker
```

```php
$user = \App\Models\User::where('role','patient')->first();
$conv = \App\Models\ChatConversation::create(['user_id' => $user?->id]);
$svc  = app(\App\Services\Chat\ChatAgentService::class);
$out  = $svc->handle($conv, 'Hola, quiero agendar una cita de psicologia', $user);
echo $out['reply'], PHP_EOL;
```

## 5. Si NO cargas la key

El agente sigue funcionando en **modo determinista**: responde con textos fijos
en espanol, ofrece cupos reales, aplica consentimiento primero, y detecta crisis
con las dos capas deterministas (palabras clave + regex). No hace ninguna llamada
de red. La key solo mejora la redaccion y anade la capa 3 de crisis.

## 6. Rotacion de la key

Trata la key como una contrasena. Para cambiarla, genera una nueva en OpenRouter,
reemplaza el valor en `.env`, ejecuta `php artisan config:clear`, y revoca la
anterior en el panel de OpenRouter.
