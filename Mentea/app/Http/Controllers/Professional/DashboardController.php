<?php

namespace App\Http\Controllers\Professional;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = Auth::user();
        $professional = $user->professionalProfile;

        $todayAppointments = collect();
        $weekCount = 0;
        $pendingCount = 0;

        if ($professional) {
            $todayAppointments = $professional->appointments()
                ->with('patient')
                ->whereDate('date', today())
                ->whereIn('status', [
                    AppointmentStatus::Pending->value,
                    AppointmentStatus::Confirmed->value,
                ])
                ->orderBy('time')
                ->get();

            $weekCount = $professional->appointments()
                ->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])
                ->whereIn('status', [
                    AppointmentStatus::Pending->value,
                    AppointmentStatus::Confirmed->value,
                    AppointmentStatus::Completed->value,
                ])
                ->count();

            $pendingCount = $professional->appointments()
                ->where('status', AppointmentStatus::Pending->value)
                ->whereDate('date', '>=', today())
                ->count();
        }

        return view('professional.dashboard', [
            'professional' => $professional,
            'todayAppointments' => $todayAppointments,
            'weekCount' => $weekCount,
            'pendingCount' => $pendingCount,
        ]);
    }
}
