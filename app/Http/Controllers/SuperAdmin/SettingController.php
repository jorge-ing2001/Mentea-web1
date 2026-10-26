<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    /** Claves gestionables con metadatos para el formulario */
    public static function definitions(): array
    {
        return [
            'cancellation_min_days' => [
                'label' => 'Días mínimos para cancelar cita',
                'group' => 'citas',
                'type' => 'integer',
                'help' => 'El paciente solo puede cancelar con al menos este número de días de anticipación.',
                'default' => '5',
            ],
            'appointment_default_duration' => [
                'label' => 'Duración por defecto de cita (minutos)',
                'group' => 'citas',
                'type' => 'integer',
                'help' => 'Se usa si el profesional no tiene duración propia.',
                'default' => '45',
            ],
            'waitlist_hold_minutes' => [
                'label' => 'Minutos de reserva en lista de espera',
                'group' => 'lista_espera',
                'type' => 'integer',
                'help' => 'Tiempo que el paciente tiene para confirmar una oferta.',
                'default' => '60',
            ],
            'reminder_hour' => [
                'label' => 'Hora de envío de recordatorios',
                'group' => 'notificaciones',
                'type' => 'string',
                'help' => 'Formato HH:MM (24 h). Requiere cron configurado.',
                'default' => '08:00',
            ],
            'clinic_name' => [
                'label' => 'Nombre de la clínica',
                'group' => 'general',
                'type' => 'string',
                'help' => 'Aparece en correos y pie de página.',
                'default' => 'Mentea',
            ],
            'support_email' => [
                'label' => 'Email de soporte',
                'group' => 'general',
                'type' => 'string',
                'help' => 'Correo visible para pacientes.',
                'default' => 'soporte@mentea.test',
            ],
            'require_documents' => [
                'label' => 'Exigir orden y autorización al agendar',
                'group' => 'citas',
                'type' => 'boolean',
                'help' => '1 = sí, 0 = no.',
                'default' => '1',
            ],
            'maintenance_message' => [
                'label' => 'Mensaje de aviso global',
                'group' => 'general',
                'type' => 'string',
                'help' => 'Texto opcional mostrado a usuarios autenticados (vacío = oculto).',
                'default' => '',
            ],
        ];
    }

    public function index(): View
    {
        $defs = self::definitions();
        $values = [];
        foreach ($defs as $key => $meta) {
            $values[$key] = SystemSetting::getValue($key, $meta['default']);
        }

        return view('super-admin.settings.index', compact('defs', 'values'));
    }

    public function update(Request $request): RedirectResponse
    {
        $defs = self::definitions();
        $rules = [];
        foreach ($defs as $key => $meta) {
            $rules[$key] = match ($meta['type']) {
                'integer' => ['nullable', 'integer', 'min:0', 'max:9999'],
                'boolean' => ['nullable', 'in:0,1'],
                default => ['nullable', 'string', 'max:500'],
            };
        }

        $data = $request->validate($rules);

        foreach ($defs as $key => $meta) {
            if (! array_key_exists($key, $data)) {
                continue;
            }
            $value = $data[$key];
            if ($value === null) {
                $value = $meta['default'];
            }
            SystemSetting::setValue($key, (string) $value, $meta['group'], $meta['type']);
        }

        return back()->with('success', 'Configuración guardada correctamente.');
    }
}
