<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Professional;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $totalPatients = User::where('role', 'patient')->count();
        $totalProfessionals = Professional::where('is_available', true)->count();
        $totalAppointments = Appointment::count();
        $appointmentsToday = Appointment::whereDate('date', today())->count();

        $byStatus = Appointment::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $upcoming = Appointment::with(['patient', 'professional'])
            ->whereDate('date', '>=', today())
            ->whereIn('status', [AppointmentStatus::Pending->value, AppointmentStatus::Confirmed->value])
            ->orderBy('date')
            ->orderBy('time')
            ->limit(8)
            ->get();

        return view('admin.dashboard', [
            'totalPatients' => $totalPatients,
            'totalProfessionals' => $totalProfessionals,
            'totalAppointments' => $totalAppointments,
            'appointmentsToday' => $appointmentsToday,
            'byStatus' => $byStatus,
            'upcoming' => $upcoming,
        ]);
    }
}
