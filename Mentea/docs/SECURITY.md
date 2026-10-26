# Seguridad y cumplimiento — Mentea

## Consentimiento (Ley 1581 / Habeas Data)
- Obligatorio en **registro** y en **perfil** (consentimiento informado + datos personales).
- Sin consentimiento informado no se puede agendar.

## Datos clínicos en correo
Los emails solo incluyen: nombre, profesional, fecha/hora, modalidad y enlace a la app.
**No** se envían notas clínicas, documentos ni motivos detallados de consulta.

## Documentos y notas clínicas
- Acceso auditado (`document.view.*` / `document.download.*` en `audit_logs`).
- Policies: paciente dueño, profesional asignado, admin/super.
- Notas clínicas: `viewClinicalNotes` / `updateClinicalNotes`.

## Idempotencia de agendamiento
- Token de formulario + caché 5 min.
- Índice único `(professional_id, date, time)` en `appointments`.

## Rotación de APP_KEY
1. `php artisan down`
2. `php artisan key:generate` (invalida sesiones y cookies cifradas)
3. Re-encriptar datos si usas casts `encrypted`
4. `php artisan up`
5. Usuarios deben volver a iniciar sesión

Nunca subas `.env` al repositorio.

## Google Meet / tokens
`GoogleMeetService` genera un enlace **placeholder**.
Producción: Google Cloud OAuth + Calendar API; secretos solo en `.env` / secret manager; no loguear tokens completos.

## Healthcheck
`GET /up` — endpoint nativo Laravel.

## Sentry (opcional)
```bash
composer require sentry/sentry-laravel
php artisan sentry:publish --dsn=TU_DSN
```
`SENTRY_LARAVEL_DSN` en `.env` de producción.
