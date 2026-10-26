# Despliegue producción — Mentea

## 1. Variables de entorno

Copia `.env.production.example` → `.env` en el servidor:

```bash
cp .env.production.example .env
php artisan key:generate
```

**Obligatorio:**

| Variable | Valor |
|----------|--------|
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_URL` | `https://tudominio.com` |
| `MENTEA_FORCE_HTTPS` | `true` |
| `QUEUE_CONNECTION` | `database` |
| `SESSION_SECURE_COOKIE` | `true` |

## 2. HTTPS

- Certificado Let's Encrypt (Certbot) o el de tu proveedor.
- El middleware `ForceHttps` redirige HTTP→HTTPS en producción.
- `URL::forceScheme('https')` activo en producción.

```bash
sudo certbot --nginx -d tudominio.com
```

## 3. Cola (emails, recordatorios, jobs)

```bash
php artisan queue:table   # si aún no
php artisan migrate
```

Supervisor (`deploy/supervisor-mentea-worker.conf`):

```bash
sudo cp deploy/supervisor-mentea-worker.conf /etc/supervisor/conf.d/mentea-worker.conf
# Ajusta la ruta /var/www/mentea
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl status mentea-worker:*
```

Sin worker: los jobs **no se ejecutan** (recordatorios, encuesta, waitlist scan vía jobs, mails en cola).

## 4. Cron (scheduler)

```bash
crontab -e
# pegar línea de deploy/crontab.example
```

El scheduler ya incluye:

- Recordatorios cada 15 min  
- Expirar ofertas lista de espera cada minuto  
- Escanear waitlist cada hora  
- Recordatorio de encuestas 10:00  

## 5. Correo SMTP

Configura `MAIL_*` y prueba desde Super → Configuración → **Enviar correo de prueba**.

## 6. Meet (honesto)

| `MENTEA_MEET_MODE` | Comportamiento |
|--------------------|----------------|
| `manual` (recomendado) | No inventa enlace; se asigna después o por otro canal |
| `off` | Igual, desactivado |
| `placeholder` | Solo local/dev; **bloqueado en production** |
| `google` | API Calendar + Meet (requiere `GOOGLE_MEET_ACCESS_TOKEN`) |

Para Google real: OAuth, Calendar API, token de acceso (o refresh flow) en secret manager — **nunca** en el repo.

## 7. Checklist go-live

```text
□ APP_DEBUG=false
□ HTTPS OK (/up responde)
□ php artisan config:cache && route:cache && view:cache
□ supervisor queue:work running
□ crontab schedule:run
□ SMTP prueba recibida
□ Meet mode distinto de placeholder
□ Backup DB + storage/app
□ Storage:link si usas public disk
```

## 8. Comandos útiles

```bash
php artisan queue:work --once
php artisan schedule:list
php artisan mentea:send-reminders
php artisan mentea:scan-waitlist
php artisan down && php artisan up
```
