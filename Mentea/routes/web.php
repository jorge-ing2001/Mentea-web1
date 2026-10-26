<?php

use App\Http\Controllers\Admin\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\ProfessionalController as AdminProfessionalController;
use App\Http\Controllers\Admin\ScheduleController as AdminScheduleController;
use App\Http\Controllers\Api\AvailabilityController;
use App\Http\Controllers\Patient\AppointmentController;
use App\Http\Controllers\Patient\DashboardController as PatientDashboard;
use App\Http\Controllers\Professional\DashboardController as ProfessionalDashboard;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/citas/confirmar/{token}', [\App\Http\Controllers\Patient\AppointmentController::class, 'confirmByToken'])->name('appointments.confirm.token');

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', function () {
    $user = Auth::user();
    if (! $user) {
        return redirect()->route('login');
    }
    return match ($user->role->value) {
        'super_admin' => redirect()->route('super.dashboard'),
        'admin' => redirect()->route('admin.dashboard'),
        'professional' => redirect()->route('professional.dashboard'),
        default => redirect()->route('patient.dashboard'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified', 'active'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    
    
    // Notificaciones in-app
    Route::get('/notificaciones', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notificaciones/{id}/leer', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notificaciones/leer-todas', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

    // Documentos de citas (orden / autorización)
    Route::get('/documentos/{appointment}/{type}', [\App\Http\Controllers\DocumentController::class, 'view'])
        ->where('type', 'order|authorization')
        ->name('documents.view');
    Route::get('/documentos/{appointment}/{type}/descargar', [\App\Http\Controllers\DocumentController::class, 'download'])
        ->where('type', 'order|authorization')
        ->name('documents.download');

    // ========== ADMIN ==========
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {

        Route::get('/diligenciamientos', [\App\Http\Controllers\Admin\PlatformEventController::class, 'index'])->name('events.index');
        Route::get('/diligenciamientos/crear', [\App\Http\Controllers\Admin\PlatformEventController::class, 'create'])->name('events.create');
        Route::post('/diligenciamientos', [\App\Http\Controllers\Admin\PlatformEventController::class, 'store'])->name('events.store');
        Route::get('/excepciones', [\App\Http\Controllers\Admin\ScheduleExceptionController::class, 'index'])->name('exceptions.index');
        Route::get('/excepciones/crear', [\App\Http\Controllers\Admin\ScheduleExceptionController::class, 'create'])->name('exceptions.create');
        Route::post('/excepciones', [\App\Http\Controllers\Admin\ScheduleExceptionController::class, 'store'])->name('exceptions.store');
        Route::delete('/excepciones/{exception}', [\App\Http\Controllers\Admin\ScheduleExceptionController::class, 'destroy'])->name('exceptions.destroy');
        Route::get('/encuestas', [\App\Http\Controllers\Admin\SurveyController::class, 'index'])->name('surveys.index');
        Route::get('/reportes', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
        Route::get('/reportes/export', [\App\Http\Controllers\Admin\ReportController::class, 'export'])->name('reports.export');

        Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
        Route::get('/calendario', [\App\Http\Controllers\Admin\CalendarController::class, 'index'])->name('calendar.index');
        Route::get('/calendario/eventos', [\App\Http\Controllers\Admin\CalendarController::class, 'events'])->name('calendar.events');

        // Profesionales
        Route::resource('professionals', AdminProfessionalController::class)->except(['show', 'destroy']);
        Route::patch('professionals/{professional}/disponibilidad', [AdminProfessionalController::class, 'toggleAvailability'])->name('professionals.toggle');
        Route::get('professionals/{professional}/horarios', [AdminScheduleController::class, 'edit'])->name('schedules.edit');
        Route::put('professionals/{professional}/horarios', [AdminScheduleController::class, 'update'])->name('schedules.update');

        // Citas
        Route::get('citas', [AdminAppointmentController::class, 'index'])->name('appointments.index');
        Route::get('citas/{appointment}', [AdminAppointmentController::class, 'show'])->name('appointments.show');

        Route::patch('citas/{appointment}/estado', [AdminAppointmentController::class, 'updateStatus'])->name('appointments.status');
        Route::get('lista-espera', [\App\Http\Controllers\Admin\WaitlistController::class, 'index'])->name('waitlist.index');
        Route::get('lista-espera/slots', [\App\Http\Controllers\Admin\WaitlistController::class, 'slots'])->name('waitlist.slots');
        Route::patch('lista-espera/{waitlist}/estado', [\App\Http\Controllers\Admin\WaitlistController::class, 'updateStatus'])->name('waitlist.status');
        Route::get('lista-espera/{waitlist}/asignar', [\App\Http\Controllers\Admin\WaitlistController::class, 'assignForm'])->name('waitlist.assign');
        Route::post('lista-espera/{waitlist}/asignar', [\App\Http\Controllers\Admin\WaitlistController::class, 'assign'])->name('waitlist.assign.store');
    });

    
    // ========== SUPER USUARIO ==========
    Route::middleware('role:super_admin')->prefix('super')->name('super.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\SuperAdmin\DashboardController::class, '__invoke'])->name('dashboard');
        Route::get('/configuracion', [\App\Http\Controllers\SuperAdmin\SettingController::class, 'index'])->name('settings.index');
        Route::put('/configuracion', [\App\Http\Controllers\SuperAdmin\SettingController::class, 'update'])->name('settings.update');
        Route::post('/mail-test', function () {
            try {
                \Illuminate\Support\Facades\Mail::raw('Prueba SMTP Mentea ' . now()->toDateTimeString(), function ($m) {
                    $m->to(auth()->user()->email)->subject('Prueba de correo — Mentea');
                });
                return back()->with('success', 'Correo de prueba enviado a ' . auth()->user()->email . '. Revisa bandeja (y spam).');
            } catch (\Throwable $e) {
                return back()->withErrors(['mail' => 'Error SMTP: ' . $e->getMessage()]);
            }
        })->name('mail.test');

        Route::get('/tickets', [\App\Http\Controllers\SuperAdmin\TicketController::class, 'index'])->name('tickets.index');
        Route::get('/tickets/crear', [\App\Http\Controllers\SuperAdmin\TicketController::class, 'create'])->name('tickets.create');
        Route::post('/tickets', [\App\Http\Controllers\SuperAdmin\TicketController::class, 'store'])->name('tickets.store');
        Route::get('/tickets/{ticket}', [\App\Http\Controllers\SuperAdmin\TicketController::class, 'show'])->name('tickets.show');
        Route::put('/tickets/{ticket}', [\App\Http\Controllers\SuperAdmin\TicketController::class, 'update'])->name('tickets.update');
        Route::get('/auditoria', [\App\Http\Controllers\SuperAdmin\AuditController::class, 'index'])->name('audit.index');
        Route::get('/auditoria/{audit}', [\App\Http\Controllers\SuperAdmin\AuditController::class, 'show'])->name('audit.show');
        Route::get('/auditoria-export', [\App\Http\Controllers\SuperAdmin\AuditController::class, 'export'])->name('audit.export');
        Route::get('/roles', [\App\Http\Controllers\SuperAdmin\RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/usuarios', [\App\Http\Controllers\SuperAdmin\RoleController::class, 'users'])->name('roles.users');
        Route::get('/roles/{role}/editar', [\App\Http\Controllers\SuperAdmin\RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{role}', [\App\Http\Controllers\SuperAdmin\RoleController::class, 'update'])->name('roles.update');
        Route::post('/roles/usuarios/{user}', [\App\Http\Controllers\SuperAdmin\RoleController::class, 'assignUser'])->name('roles.assign');
        Route::get('/usuarios', [\App\Http\Controllers\SuperAdmin\UserController::class, 'index'])->name('users.index');
        Route::get('/usuarios/crear', [\App\Http\Controllers\SuperAdmin\UserController::class, 'create'])->name('users.create');
        Route::post('/usuarios', [\App\Http\Controllers\SuperAdmin\UserController::class, 'store'])->name('users.store');


    });

    // ========== PROFESIONAL ==========
    Route::middleware('role:professional')->prefix('profesional')->name('professional.')->group(function () {
        Route::get('/dashboard', ProfessionalDashboard::class)->name('dashboard');
        Route::get('/calendario', [\App\Http\Controllers\Professional\CalendarController::class, 'index'])->name('calendar.index');
        Route::get('/calendario/eventos', [\App\Http\Controllers\Professional\CalendarController::class, 'events'])->name('calendar.events');
        Route::get('/citas', [\App\Http\Controllers\Professional\AppointmentController::class, 'index'])->name('appointments.index');
        Route::get('/citas/{appointment}', [\App\Http\Controllers\Professional\AppointmentController::class, 'show'])->name('appointments.show');
        Route::post('/citas/{appointment}/evolucion', [\App\Http\Controllers\Professional\ClinicalNoteController::class, 'store'])->name('clinical.store');
        Route::patch('/citas/{appointment}/estado', [\App\Http\Controllers\Professional\AppointmentController::class, 'updateStatus'])->name('appointments.status');
    });

    // ========== PACIENTE ==========
    Route::middleware('role:patient')->prefix('paciente')->name('patient.')->group(function () {
        Route::get('/dashboard', PatientDashboard::class)->name('dashboard');
        Route::get('/citas', [AppointmentController::class, 'index'])->name('appointments.index');
        Route::get('/citas/agendar', [AppointmentController::class, 'create'])->name('appointments.create');
        Route::get('/citas/agendar/{professional}', [AppointmentController::class, 'schedule'])->name('appointments.schedule');
        Route::get('/citas/agendar/{professional}/slots', [AppointmentController::class, 'slots'])->middleware('throttle:60,1')->name('appointments.slots');
        Route::post('/citas', [AppointmentController::class, 'store'])->middleware('throttle:10,1')->name('appointments.store');
        Route::get('/citas/{appointment}', [AppointmentController::class, 'show'])->name('appointments.show');
        Route::post('/citas/{appointment}/cancelar', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
        Route::post('/citas/{appointment}/confirmar', [AppointmentController::class, 'confirm'])->name('appointments.confirm');

        Route::get('/citas/{appointment}/reprogramar', [AppointmentController::class, 'edit'])->name('appointments.edit');
        Route::put('/citas/{appointment}', [AppointmentController::class, 'update'])->name('appointments.update');
        Route::get('/citas/{appointment}/encuesta', [\App\Http\Controllers\Patient\SurveyController::class, 'create'])->name('surveys.create');
        Route::post('/citas/{appointment}/encuesta', [\App\Http\Controllers\Patient\SurveyController::class, 'store'])->name('surveys.store');
        Route::get('/onboarding', [\App\Http\Controllers\Patient\OnboardingController::class, 'show'])->name('onboarding.show');
        Route::post('/onboarding', [\App\Http\Controllers\Patient\OnboardingController::class, 'store'])->name('onboarding.store');
        Route::get('/calendario', [\App\Http\Controllers\Patient\CalendarController::class, 'index'])->name('calendar.index');
        Route::get('/calendario/eventos', [\App\Http\Controllers\Patient\CalendarController::class, 'events'])->name('calendar.events');
        Route::get('/lista-espera', [\App\Http\Controllers\Patient\WaitlistController::class, 'index'])->name('waitlist.index');
        Route::get('/lista-espera/nueva', [\App\Http\Controllers\Patient\WaitlistController::class, 'create'])->name('waitlist.create');
        Route::post('/lista-espera', [\App\Http\Controllers\Patient\WaitlistController::class, 'store'])->name('waitlist.store');
        Route::get('/lista-espera/{waitlist}', [\App\Http\Controllers\Patient\WaitlistController::class, 'show'])->name('waitlist.show');
        Route::post('/lista-espera/{waitlist}/confirmar', [\App\Http\Controllers\Patient\WaitlistController::class, 'confirm'])->name('waitlist.confirm');
        Route::post('/lista-espera/{waitlist}/rechazar', [\App\Http\Controllers\Patient\WaitlistController::class, 'reject'])->name('waitlist.reject');
        Route::post('/lista-espera/{waitlist}/cancelar', [\App\Http\Controllers\Patient\WaitlistController::class, 'cancel'])->name('waitlist.cancel');

        Route::get('/soporte', [\App\Http\Controllers\Patient\SupportTicketController::class, 'index'])->name('tickets.index');
        Route::get('/soporte/nuevo', [\App\Http\Controllers\Patient\SupportTicketController::class, 'create'])->name('tickets.create');
        Route::post('/soporte', [\App\Http\Controllers\Patient\SupportTicketController::class, 'store'])->name('tickets.store');

    });

    // API disponibilidad
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/profesionales/{professional}/fechas', [AvailabilityController::class, 'dates'])->name('availability.dates');
        Route::get('/profesionales/{professional}/horarios', [AvailabilityController::class, 'slots'])->name('availability.slots');
    });
});

require __DIR__.'/auth.php';



