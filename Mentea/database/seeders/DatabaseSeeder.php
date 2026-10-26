<?php

namespace Database\Seeders;

use App\Enums\AppointmentStatus;
use App\Enums\WaitlistStatus;
use App\Enums\Modality;
use App\Enums\Role;
use App\Models\Appointment;
use App\Models\Professional;
use App\Models\Schedule;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\WaitlistEntry;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {

        $password = Hash::make('password');
        $now = now();

        // —— Usuarios base ——
        $admin = 
        User::create([
            'name' => 'Super Usuario Mentea',
            'email' => 'super@mentea.test',
            'password' => $password,
            'role' => Role::SuperAdmin,
            'document_number' => '00000000',
            'email_verified_at' => $now,
            'is_active' => true,
        ]);

        User::create([
            'document_number' => '1000000000',
            'name' => 'Administrador Mentea',
            'email' => 'admin@mentea.test',
            'email_verified_at' => $now,
            'password' => $password,
            'phone' => '3000000000',
            'role' => Role::Admin,
            'is_active' => true,
        ]);

        $patient = User::create([
            'document_number' => '1098765432',
            'name' => 'Paciente Demo',
            'email' => 'paciente@mentea.test',
            'email_verified_at' => $now,
            'password' => $password,
            'phone' => '3101234567',
            'role' => Role::Patient,
            'is_active' => true,
        ]);

        $patient2 = User::create([
            'document_number' => '1007650491',
            'name' => 'Laura Gómez',
            'email' => 'laura@mentea.test',
            'email_verified_at' => $now,
            'password' => $password,
            'phone' => '3109876543',
            'role' => Role::Patient,
            'is_active' => true,
        ]);

        $patient3 = User::create([
            'document_number' => '1234567890',
            'name' => 'Carlos Méndez',
            'email' => 'carlos@mentea.test',
            'email_verified_at' => $now,
            'password' => $password,
            'phone' => '3112223333',
            'role' => Role::Patient,
            'is_active' => true,
        ]);

        // Profesional principal (login demo)
        $proUser = User::create([
            'document_number' => '1087654321',
            'name' => 'Dr. Juan Pérez',
            'email' => 'profesional@mentea.test',
            'email_verified_at' => $now,
            'password' => $password,
            'phone' => '3221111111',
            'role' => Role::Professional,
            'is_active' => true,
        ]);

        // —— Profesionales del proyecto original (saludmental) ——
        $professionalsData = [
            [
                'user_id' => $proUser->id,
                'name' => 'Dr. Juan Pérez',
                'specialty' => 'Psicología Clínica',
                'profession' => 'Psicología',
                'description' => 'Especialista en evaluación y tratamiento de trastornos emocionales y de conducta. Ofrece terapia cognitivo-conductual para adultos con ansiedad, depresión y estrés.',
                'email' => 'juan.perez@saludmental.com',
                'phone' => '322-111-1111',
                'hours' => ['start' => '08:00', 'end' => '17:00'],
            ],
            [
                'name' => 'Dra. María López',
                'specialty' => 'Terapia Familiar',
                'profession' => 'Psicología',
                'description' => 'Experta en terapia familiar sistémica. Ayuda a mejorar la comunicación, resolver conflictos y fortalecer los lazos familiares.',
                'email' => 'maria.lopez@saludmental.com',
                'phone' => '322-222-2222',
                'hours' => ['start' => '09:00', 'end' => '18:00'],
            ],
            [
                'name' => 'Dr. Andrés Gómez',
                'specialty' => 'Psicología Infantil',
                'profession' => 'Psicología',
                'description' => 'Psicólogo infantil con experiencia en desarrollo emocional, comportamiento y aprendizaje en niños y adolescentes.',
                'email' => 'andres.gomez@saludmental.com',
                'phone' => '322-333-3333',
                'hours' => ['start' => '10:00', 'end' => '18:00'],
            ],
            [
                'name' => 'Dra. Carolina Torres',
                'specialty' => 'Terapia de Pareja',
                'profession' => 'Psicología',
                'description' => 'Especialista en relaciones de pareja, resolución de conflictos y mejora de la comunicación afectiva.',
                'email' => 'carolina.torres@saludmental.com',
                'phone' => '322-444-4444',
                'hours' => ['start' => '08:00', 'end' => '16:00'],
            ],
            [
                'name' => 'Dr. Luis Ramírez',
                'specialty' => 'Psicología Cognitiva',
                'profession' => 'Psicología',
                'description' => 'Psicólogo cognitivo con enfoque en procesos mentales y comportamiento.',
                'email' => 'luis.ramirez@saludmental.com',
                'phone' => '322-555-5555',
                'hours' => ['start' => '14:00', 'end' => '20:00'],
            ],
            [
                'name' => 'Dr. Ricardo Molina',
                'specialty' => 'Psicología Organizacional',
                'profession' => 'Psicología',
                'description' => 'Psicólogo organizacional especializado en clima laboral y desarrollo del talento humano.',
                'email' => 'ricardo.molina@saludmental.com',
                'phone' => '322-777-7777',
                'hours' => ['start' => '09:00', 'end' => '19:00'],
            ],
            [
                'name' => 'Dr. Sebastián Herrera',
                'specialty' => 'Psiquiatría Clínica',
                'profession' => 'Psiquiatría',
                'description' => 'Psiquiatra especializado en trastornos de ansiedad, depresión y manejo farmacológico.',
                'email' => 'sebastian.herrera@saludmental.com',
                'phone' => '320-800-1111',
                'hours' => ['start' => '08:00', 'end' => '16:00'],
            ],
            [
                'name' => 'Dra. Valentina Rojas',
                'specialty' => 'Psiquiatría General',
                'profession' => 'Psiquiatría',
                'description' => 'Experiencia en trastornos afectivos, intervención en crisis y esquizofrenia.',
                'email' => 'valentina.rojas@saludmental.com',
                'phone' => '320-800-2222',
                'hours' => ['start' => '09:00', 'end' => '17:00'],
            ],
            [
                'name' => 'Dr. Manuel Castaño',
                'specialty' => 'Psiquiatría Infantil y Adolescente',
                'profession' => 'Psiquiatría',
                'description' => 'Especialista en trastornos del neurodesarrollo, déficit de atención y conductas disruptivas.',
                'email' => 'manuel.castano@saludmental.com',
                'phone' => '320-800-3333',
                'hours' => ['start' => '08:00', 'end' => '15:00'],
            ],
            [
                'name' => 'Dra. Sofía Delgado',
                'specialty' => 'Neuropsicología Clínica',
                'profession' => 'Neuropsicología',
                'description' => 'Neuropsicóloga experta en evaluación cognitiva, memoria, funciones ejecutivas y rehabilitación.',
                'email' => 'sofia.delgado@saludmental.com',
                'phone' => '320-900-1111',
                'hours' => ['start' => '08:00', 'end' => '16:00'],
            ],
            [
                'name' => 'Dr. Miguel Arrieta',
                'specialty' => 'Neuropsicología Infantil',
                'profession' => 'Neuropsicología',
                'description' => 'Especializado en trastornos del aprendizaje, desarrollo cognitivo y evaluación neuropsicológica infantil.',
                'email' => 'miguel.arrieta@saludmental.com',
                'phone' => '320-900-2222',
                'hours' => ['start' => '09:00', 'end' => '17:00'],
            ],
        ];

        $professionals = [];
        foreach ($professionalsData as $data) {
            $hours = $data['hours'];
            unset($data['hours']);
            $pro = Professional::create(array_merge($data, [
                'is_available' => true,
                'appointment_duration_minutes' => 45,
            ]));
            $professionals[] = $pro;

            // Lun–Vie
            foreach ([1, 2, 3, 4, 5] as $day) {
                Schedule::create([
                    'professional_id' => $pro->id,
                    'day_of_week' => $day,
                    'start_time' => $hours['start'],
                    'end_time' => $hours['end'],
                    'is_active' => true,
                ]);
            }
        }

        $juan = $professionals[0]; // Dr. Juan Pérez → profesional@mentea.test
        $maria = $professionals[1];
        $andres = $professionals[2];
        $sebastian = $professionals[6];
        $sofia = $professionals[9];

        // —— Citas de demo (fechas relativas a hoy para que siempre haya contenido) ——
        $citas = [
            // Paciente demo — próximas
            [
                'patient_id' => $patient->id,
                'professional_id' => $juan->id,
                'date' => Carbon::today()->next(Carbon::MONDAY)->toDateString(),
                'time' => '09:00',
                'modality' => Modality::InPerson,
                'status' => AppointmentStatus::Confirmed,
                'reason' => 'Seguimiento ansiedad y estrés laboral',
            ],
            [
                'patient_id' => $patient->id,
                'professional_id' => $maria->id,
                'date' => Carbon::today()->addDays(10)->toDateString(),
                'time' => '10:00',
                'modality' => Modality::Virtual,
                'status' => AppointmentStatus::Pending,
                'reason' => 'Terapia familiar — primera sesión',
                'meet_link' => 'https://meet.google.com/demo-mentea-familia',
            ],
            [
                'patient_id' => $patient->id,
                'professional_id' => $juan->id,
                'date' => Carbon::today()->subDays(14)->toDateString(),
                'time' => '11:00',
                'modality' => Modality::InPerson,
                'status' => AppointmentStatus::Completed,
                'reason' => 'Consulta inicial',
            ],
            // Hoy para el profesional Juan
            [
                'patient_id' => $patient2->id,
                'professional_id' => $juan->id,
                'date' => Carbon::today()->toDateString(),
                'time' => '10:00',
                'modality' => Modality::InPerson,
                'status' => AppointmentStatus::Confirmed,
                'reason' => 'Control de seguimiento',
            ],
            [
                'patient_id' => $patient3->id,
                'professional_id' => $juan->id,
                'date' => Carbon::today()->toDateString(),
                'time' => '14:00',
                'modality' => Modality::Virtual,
                'status' => AppointmentStatus::Pending,
                'reason' => 'Primera evaluación',
                'meet_link' => 'https://meet.google.com/demo-mentea-eval',
            ],
            // Otras citas admin
            [
                'patient_id' => $patient2->id,
                'professional_id' => $sebastian->id,
                'date' => Carbon::today()->addDays(3)->toDateString(),
                'time' => '09:00',
                'modality' => Modality::InPerson,
                'status' => AppointmentStatus::Confirmed,
                'reason' => 'Evaluación psiquiátrica',
            ],
            [
                'patient_id' => $patient3->id,
                'professional_id' => $sofia->id,
                'date' => Carbon::today()->addDays(5)->toDateString(),
                'time' => '11:00',
                'modality' => Modality::InPerson,
                'status' => AppointmentStatus::Pending,
                'reason' => 'Evaluación neuropsicológica',
            ],
            [
                'patient_id' => $patient2->id,
                'professional_id' => $andres->id,
                'date' => Carbon::today()->subDays(7)->toDateString(),
                'time' => '15:00',
                'modality' => Modality::InPerson,
                'status' => AppointmentStatus::Completed,
                'reason' => 'Consulta infantil',
            ],
            [
                'patient_id' => $patient3->id,
                'professional_id' => $maria->id,
                'date' => Carbon::today()->addDays(12)->toDateString(),
                'time' => '16:00',
                'modality' => Modality::Virtual,
                'status' => AppointmentStatus::Cancelled,
                'reason' => 'Terapia familiar',
                'cancelled_at' => now()->subDay(),
                'cancellation_reason' => 'Cambio de agenda del paciente',
            ],
        ];

        foreach ($citas as $c) {
            Appointment::create($c);
        }

        // Lista de espera
        WaitlistEntry::create([
            'patient_id' => $patient->id,
            'professional_id' => $juan->id,
            'specialty' => 'Psicología Clínica',
            'date_from' => Carbon::today()->toDateString(),
            'date_to' => Carbon::today()->addDays(30)->toDateString(),
            'time_from' => '08:00',
            'time_to' => '12:00',
            'modality' => Modality::InPerson,
            'status' => WaitlistStatus::Waiting,
            'notes' => 'Prefiero horario de mañana',
        ]);
        WaitlistEntry::create([
            'patient_id' => $patient2->id,
            'professional_id' => null,
            'specialty' => 'Psiquiatría Clínica',
            'date_from' => Carbon::today()->toDateString(),
            'date_to' => Carbon::today()->addDays(45)->toDateString(),
            'time_from' => '09:00',
            'time_to' => '17:00',
            'modality' => Modality::Virtual,
            'status' => WaitlistStatus::Waiting,
            'notes' => 'Cualquier profesional disponible',
        ]);

        SystemSetting::setValue('smtp_from_nombre', 'Mentea', 'mail');
        SystemSetting::setValue('smtp_from_correo', 'noreply@mentea.test', 'mail');
        SystemSetting::setValue('cancellation_min_days', '5', 'appointments', 'integer');
        SystemSetting::setValue('modification_max_hours', '24', 'appointments', 'integer');

        $this->command?->info('Seed OK: 11 profesionales, citas demo, lista de espera.');
        $this->command?->info('Login: super@mentea.test / admin@mentea.test / profesional@mentea.test / paciente@mentea.test — password');

        $this->call(RolesAndPermissionsSeeder::class);
    }
}
