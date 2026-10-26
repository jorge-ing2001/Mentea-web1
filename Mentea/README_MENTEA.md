# Mentea

**Plataforma web de agendamiento de citas de salud mental**  
Laravel · Pacientes · Profesionales · Administración

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

php artisan migrate --seed
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

## Roles Spatie

Paquete: `spatie/laravel-permission`

```bash
php artisan migrate
php artisan db:seed --class=RolesAndPermissionsSeeder
```

Panel super usuario → **Roles** (`/super/roles`): editar permisos y asignar roles a usuarios.
La columna `users.role` se mantiene sincronizada como rol principal.
