# Mentea

**Plataforma web de agendamiento de citas de salud mental**  
Laravel · Pacientes · Profesionales · Administración

---

## Despliegue y documentación

- **Despliegue en Render + PostgreSQL:** [docs/DESPLIEGUE_RENDER.md](docs/DESPLIEGUE_RENDER.md) (requisitos, correr en local con SQLite, Blueprint `render.yaml` y lista exacta de variables de entorno).
- **Activar el agente de IA:** [docs/ACTIVAR_IA.md](docs/ACTIVAR_IA.md).

---

## Características

- **Pacientes:** registro, agendar cita (especialidad → profesional → fecha/hora), cancelar (≥5 días), lista de espera, encuesta post-cita
- **Profesionales:** agenda del día, listado de citas, confirmar / completar / no asistió, notas clínicas
- **Admin:** dashboard, CRUD profesionales, horarios, gestión de citas y estados, lista de espera
- **Sistema:** roles y policies, notificaciones email, recordatorios diarios, auditoría, tests de flujos críticos
- **UX:** landing pública, navegación por rol, interfaz responsive (Tailwind / Breeze)

---

## Requisitos

- PHP 8.2+
- Composer 2
- SQLite (desarrollo) o MySQL/MariaDB (producción)
- Extensiones: mbstring, xml, curl, zip, bcmath, pdo_sqlite o pdo_mysql

---

## Instalación rápida

```bash
composer install
cp .env.example .env
php artisan key:generate

# SQLite (desarrollo)
touch database/database.sqlite
# En .env: DB_CONNECTION=sqlite

# O MySQL: configura DB_* en .env

php artisan migrate:fresh --seed
php artisan serve
```

Abre http://localhost:8000

### Credenciales demo

| Rol          | Email                      | Password  |
|--------------|----------------------------|-----------|
| Super usuario| super@mentea.test          | password  |
| Admin        | admin@mentea.test          | password  |
| Profesional  | profesional@mentea.test    | password  |
| Paciente     | paciente@mentea.test       | password  |

---

## Configuración email (producción)

En `.env`:

```
MAIL_MAILER=smtp
MAIL_HOST=tu-smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_FROM_ADDRESS=noreply@tudominio.com
MAIL_FROM_NAME=Mentea
```

Recordatorios (cron):

```
* * * * * cd /ruta/a/Mentea && php artisan schedule:run >> /dev/null 2>&1
```

Comando manual: `php artisan mentea:send-reminders`

---

## Estructura principal

```
app/
├── Enums/           Role, AppointmentStatus, Modality
├── Models/          User, Professional, Appointment, Schedule, ...
├── Services/        AvailabilityService, AppointmentService, GoogleMeetService
├── Http/
│   ├── Controllers/ Admin/, Patient/, Professional/, Api/, Auth/
│   ├── Middleware/  role, active
│   ├── Requests/
│   └── Policies/
├── Notifications/   Booked, Reminder, Cancelled
├── Jobs/            SendAppointmentReminders
└── Console/Commands/
database/migrations/
tests/Feature/       AppointmentBooking, AppointmentCancel
```

---

## Rutas destacadas

| Área        | Rutas |
|-------------|--------|
| Pública     | `/` landing, `/login`, `/register` |
| Paciente    | `/paciente/citas/agendar`, `/paciente/citas`, lista-espera, encuesta |
| Profesional | `/profesional/dashboard`, `/profesional/citas` |
| Admin       | `/admin/dashboard`, profesionales, citas, lista-espera |

---

## Tests

```bash
php artisan test --filter=Appointment
```

---

## Google Meet (opcional)

El servicio actual genera un enlace placeholder. Para producción, configura OAuth en `.env` y completa `App\Services\GoogleMeetService` con `google/apiclient`.

---

## Seguridad

- Contraseñas con `password_hash` / bcrypt
- Policies por recurso
- Middleware de rol y cuenta activa
- Validación en Form Requests
- Secrets solo en `.env` (nunca en el repositorio)

---

## Licencia / origen

Reestructuración profesional del proyecto original de agendamiento de citas, migrado a Laravel con arquitectura limpia y lista para evolucionar.

---

## Mejoras A / C / D (incluidas)

### A — Reprogramar cita
- Paciente: botón **Reprogramar** en el detalle (solo en las primeras 24 h y si está pendiente/confirmada).
- Rutas: `GET/PUT /paciente/citas/{id}/reprogramar`.
- Email de notificación de cambio de fecha/hora.
- Admin puede reprogramar sin límite de 24 h (vía policy).

### C — Calendario visual
- Admin: `/admin/calendario` (FullCalendar, semana/mes/día).
- Profesional: `/profesional/calendario` (solo sus citas).
- Colores por estado; clic abre el detalle.

### D — Lista de espera automática
- Al **cancelar** una cita, `WaitlistService` busca entradas compatibles (mismo profesional, misma especialidad o sin filtro) y:
  - Envía email “Hay un hueco disponible”.
  - Marca la entrada como `notified`.

## Actualización — documentos, anti-doble cita, activar/inactivar, lista de espera

### Documentos obligatorios al agendar
- Orden y Autorización (PDF o imagen, máx. 10 MB).
- Sin ambos archivos no se completa el agendamiento.
- Admin y Profesional: Ver / Descargar en el detalle de la cita.

### Sin citas simultáneas del mismo paciente
- Validación por fecha + hora (presencial o virtual).
- Mensaje: "Ya tienes una cita programada en esa fecha y hora..."

### Profesionales: Activar / Inactivar
- Ya no se elimina el registro.
- Inactivo no aparece para nuevas citas; historial se conserva.

### Lista de espera — asignar cita
- Admin → Lista de espera → **Asignar cita** → elige profesional, fecha y hora libre.
- Crea la cita, marca la solicitud como `assigned` y guarda trazabilidad.

Tras actualizar:

```bash
php artisan migrate
# o si quieres datos demo de nuevo:
php artisan migrate:fresh --seed
```

## Email HTML + notificaciones in-app

- Plantillas HTML en `resources/views/emails/`.
- Canal `database` + campana en el menú (no leídas).
- Rutas: `/notificaciones`, marcar leída, marcar todas.
- Recordatorios: `php artisan mentea:send-reminders` (programado 08:00).

```bash
php artisan migrate
# MAIL_* en .env para correo real; en local puedes usar MAIL_MAILER=log
```

## Estilos CSS personalizados (Mentea)

- Tema Tailwind: colores `mentea` / `calm`, fuentes DM Sans + Fraunces.
- Componentes CSS: `.btn-mentea`, `.mentea-card`, `.mentea-stat`, etc. en `resources/css/app.css`.
- Fallback estático: `public/css/mentea.css` (se carga aunque no haya `npm run build`).

Con Node instalado:

```bash
npm install
npm run build   # producción
npm run dev     # desarrollo con hot reload
```
