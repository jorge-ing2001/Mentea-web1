# Despliegue en Render + PostgreSQL (guía)

Esta guía explica cómo correr Mentea en local con SQLite y cómo desplegarla en
[Render](https://render.com) usando el Blueprint `render.yaml` con una base de
datos PostgreSQL gestionada. Incluye la lista exacta de variables de entorno.

La aplicación Laravel vive en la **raíz del repositorio** (no hay subcarpeta).

---

## 1. Requisitos

- **PHP 8.3+** (la imagen de producción usa `php:8.3-apache`).
- **Composer 2**.
- **Node 20** (para compilar los assets con Vite).
- Base de datos:
  - **PostgreSQL** en producción (Render la provisiona de forma gestionada).
  - **SQLite** en local (sin servidor de base de datos, archivo único).
- Extensiones PHP: `mbstring`, `xml`, `curl`, `zip`, `bcmath`, `gd`,
  `pdo_sqlite` (local) y `pdo_pgsql` / `pgsql` (producción).

---

## 2. Correr en local (SQLite)

```bash
# 1. Clonar el repositorio
git clone <url-del-repo>
cd <carpeta-del-repo>

# 2. Dependencias PHP
composer install

# 3. Variables de entorno
cp .env.example .env
php artisan key:generate

# 4. Base de datos SQLite (el .env.example ya trae DB_CONNECTION=sqlite)
touch database/database.sqlite
php artisan migrate

# (opcional) datos demo + usuarios de prueba
# php artisan migrate:fresh --seed

# 5. Frontend (Vite)
npm install
npm run build        # o: npm run dev (hot reload en desarrollo)

# 6. Servir la app
php artisan serve
```

Abre http://localhost:8000

### El chat / IA es opcional

El agente conversacional (Mentea AI) funciona **sin** `OPENROUTER_API_KEY`:
cuando la clave no está definida, el agente degrada de forma segura y usa su
ruta determinista (información, detección de crisis y consentimiento) sin llamar
a la red. No falla ni bloquea el resto de la aplicación.

Para habilitar las respuestas generadas por el modelo, define
`OPENROUTER_API_KEY` (ver [docs/ACTIVAR_IA.md](ACTIVAR_IA.md)).

---

## 3. Despliegue en Render con `render.yaml` (Blueprint)

El archivo `render.yaml` en la raíz es un **Blueprint** de Render que aprovisiona
todo automáticamente:

- Un **servicio web** `mentea-web` construido con Docker (`./Dockerfile`,
  multi-stage: etapa Node 20 para compilar Vite + etapa `php:8.3-apache`).
- Una **base de datos PostgreSQL gestionada** `mentea-db` (plan free).
- El `healthCheckPath` es `/up`.

### Pasos

1. Sube el repositorio a GitHub (o GitLab).
2. En Render: **New > Blueprint** y conecta el repositorio. Render detecta
   `render.yaml` y crea el servicio web + la base de datos PostgreSQL.
3. La conexión a la base de datos se cablea sola: el Blueprint expone
   `DATABASE_URL` (y las partes `DB_HOST`, `DB_PORT`, `DB_DATABASE`,
   `DB_USERNAME`, `DB_PASSWORD`) desde la base `mentea-db` mediante
   `fromDatabase`. `config/database.php` lee `DATABASE_URL` de forma nativa para
   la conexión `pgsql`, así que no hay que rellenar nada a mano.
4. `APP_KEY` se genera automáticamente (`generateValue: true`). No la pegues tú.
5. Define los secretos marcados como `sync: false` en el dashboard de Render:
   - **`OPENROUTER_API_KEY`**: pégala como variable secreta (formato
     `sk-or-v1-...`). Opcional; si no la pones, el chat usa la ruta determinista.
   - **`APP_URL`**: ponla con la URL pública que Render asigna al servicio una
     vez creada (por ejemplo `https://mentea-web.onrender.com`).
6. Render construye la imagen Docker y lanza el servicio. En el arranque del
   contenedor se ejecutan las migraciones y el cacheo de configuración (ver
   sección **Migraciones y datos**).

---

## 4. Variables de entorno (tabla exacta)

Marca como **secreto** toda variable con valor sensible. En Render, los secretos
se configuran como `sync: false` (no se guardan en `render.yaml`) o se inyectan
automáticamente desde la base de datos gestionada.

| Variable | Propósito | Ejemplo / Valor | ¿Secreto? |
|----------|-----------|-----------------|-----------|
| `APP_NAME` | Nombre de la aplicación | `Mentea` | No |
| `APP_ENV` | Entorno de ejecución | `production` | No |
| `APP_KEY` | Clave de cifrado de Laravel. En Render se genera sola (`generateValue: true`). En local: `php artisan key:generate --show` y pegar | `base64:...` | **Sí** |
| `APP_DEBUG` | Modo depuración (debe ir en false en producción) | `false` | No |
| `APP_URL` | URL pública del servicio en Render | `https://mentea-web.onrender.com` | No |
| `APP_LOCALE` | Idioma por defecto | `es` | No |
| `APP_FALLBACK_LOCALE` | Idioma de respaldo | `es` | No |
| `LOG_CHANNEL` | Canal de logs | `stack` | No |
| `LOG_LEVEL` | Nivel mínimo de log | `error` | No |
| `DB_CONNECTION` | Driver de base de datos | `pgsql` | No |
| `DATABASE_URL` | Cadena de conexión completa a PostgreSQL (inyectada por Render desde `mentea-db`). La app la lee de forma nativa | `postgres://usuario:password@host:5432/mentea` | **Sí** |
| `DB_HOST` | Host de la base (inyectado desde `mentea-db`; alternativa a `DATABASE_URL`) | `dpg-xxxx.oregon-postgres.render.com` | No |
| `DB_PORT` | Puerto de PostgreSQL | `5432` | No |
| `DB_DATABASE` | Nombre de la base | `mentea` | No |
| `DB_USERNAME` | Usuario de la base (inyectado desde `mentea-db`) | `mentea` | No |
| `DB_PASSWORD` | Contraseña de la base (inyectada desde `mentea-db`) | `(generada por Render)` | **Sí** |
| `SESSION_DRIVER` | Almacenamiento de sesiones | `database` | No |
| `SESSION_SECURE_COOKIE` | Cookies de sesión solo por HTTPS | `true` | No |
| `CACHE_STORE` | Almacén de caché | `database` | No |
| `QUEUE_CONNECTION` | Conexión de colas | `database` | No |
| `MAIL_MAILER` | Transporte de correo | `smtp` | No |
| `MAIL_HOST` | Servidor SMTP | `smtp.tudominio.com` | No |
| `MAIL_PORT` | Puerto SMTP | `587` | No |
| `MAIL_USERNAME` | Usuario SMTP | `apikey` | **Sí** |
| `MAIL_PASSWORD` | Contraseña SMTP | `(credencial del proveedor)` | **Sí** |
| `MAIL_ENCRYPTION` | Cifrado SMTP | `tls` | No |
| `MAIL_FROM_ADDRESS` | Remitente de los correos | `noreply@tudominio.com` | No |
| `MAIL_FROM_NAME` | Nombre del remitente | `${APP_NAME}` | No |
| `MENTEA_FORCE_HTTPS` | Forzar HTTPS en producción | `true` | No |
| `OPENROUTER_API_KEY` | Clave de OpenRouter para el chat/IA. Nunca en git; pegar en el dashboard | `sk-or-v1-...` | **Sí** |
| `OPENROUTER_MODEL` | Modelo usado por el agente | `openai/gpt-4o-mini` | No |
| `OPENROUTER_BASE_URL` | Endpoint base de OpenRouter | `https://openrouter.ai/api/v1` | No |
| `CHAT_HISTORY_WINDOW` | Nº de mensajes de historial que recuerda el agente | `12` | No |
| `CHAT_HTTP_TIMEOUT` | Timeout (segundos) de las llamadas al modelo | `20` | No |
| `CHAT_CRISIS_LINE` | Línea de crisis que muestra el agente | `106` | No |
| `CHAT_CRISIS_CLASSIFIER` | Activa el clasificador de crisis | `true` | No |

> **Base de datos:** basta con `DATABASE_URL` (secreto) para conectar PostgreSQL.
> Las variables `DB_HOST`/`DB_PORT`/`DB_DATABASE`/`DB_USERNAME`/`DB_PASSWORD` son
> una alternativa equivalente; el Blueprint inyecta ambas desde `mentea-db`.

---

## 5. Migraciones y datos

El `CMD` del Dockerfile ejecuta en el **arranque del contenedor**:

```
php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan view:cache && apache2-foreground
```

Es decir, las migraciones se aplican automáticamente en cada despliegue. Esto es
seguro porque Render inyecta las variables de la base de datos (`DATABASE_URL`,
etc.) en tiempo de ejecución, no en el build. El cacheo de configuración, rutas
y vistas también se hace en el arranque por el mismo motivo.

### Primer usuario / datos de prueba

El proyecto incluye `Database\Seeders\DatabaseSeeder`, que crea los usuarios
base (incluido el super usuario `super@mentea.test` con rol `super_admin`) y
los roles/permisos vía `RolesAndPermissionsSeeder`. Para poblar la base:

```bash
php artisan db:seed            # ejecuta DatabaseSeeder
# o, recreando desde cero:
php artisan migrate:fresh --seed
```

En producción ejecuta el seeder solo cuando necesites los datos iniciales (por
ejemplo, desde el Shell del servicio en Render). El arranque normal solo corre
`migrate --force`, no los seeders.

---

## 6. Seguridad

- `APP_KEY` y `OPENROUTER_API_KEY` **nunca** deben subirse al repositorio.
- El archivo `.env` está en `.gitignore`: no se versiona.
- Los secretos se configuran en el dashboard de Render (`sync: false`) o se
  inyectan desde la base de datos gestionada; nunca en texto plano en `render.yaml`.
- Rota (regenera) cualquier clave que haya sido compartida alguna vez en texto
  plano (por chat, correo, capturas, etc.).

---

## Referencias

- [docs/ACTIVAR_IA.md](ACTIVAR_IA.md): cómo activar el agente de IA.
- [docs/CHAT_AGENT.md](CHAT_AGENT.md): detalles del agente de chat.
- `render.yaml`: Blueprint de Render.
- `.env.production.example`: plantilla de variables de producción.
