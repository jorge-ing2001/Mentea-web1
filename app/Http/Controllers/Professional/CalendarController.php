<?php

namespace App\Http\Controllers\Professional;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CalendarController extends Controller
{
    private function professionalOrAbort()
    {
        $professional = Auth::user()->professionalProfile;
        abort_unless($professional, 403, 'Cuenta no vinculada a perfil profesional.');
        return $professional;
    }

    public function index(): View
    {
        $this->professionalOrAbort();
        return view('professional.calendar.index');
    }

    public function events(Request $request): JsonResponse
    {
        $professional = $this->professionalOrAbort();
        $start = $request->query('start', now()->startOfMonth()->toDateString());
        $end = $request->query('end', now()->endOfMonth()->toDateString());

        $appointments = $professional->appointments()
            ->with('patient')
            ->whereDate('date', '>=', $start)
            ->whereDate('date', '<=', $end)
            ->whereIn('status', [
                AppointmentStatus::Pending->value,
                AppointmentStatus::Confirmed->value,
                AppointmentStatus::Completed->value,
            ])
            ->get();

        $events = $appointments->map(function (Appointment $a) use ($professional) {
            $startDt = $a->date->format('Y-m-d') . 'T' . \Carbon\Carbon::parse($a->time)->format('H:i:s');
            $endDt = \Carbon\Carbon::parse($startDt)
                ->addMinutes($professional->appointment_duration_minutes ?? 45)
                ->format('Y-m-d\TH:i:s');

            $color = match ($a->status) {
                AppointmentStatus::Pending => '#f59e0b',
                AppointmentStatus::Confirmed => '#4f46e5',
                AppointmentStatus::Completed => '#16a34a',
                default => '#6b7280',
            };

            return [
                'id' => $a->id,
                'title' => $a->patient->name ?? 'Paciente',
                'start' => $startDt,
                'end' => $endDt,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'url' => route('professional.appointments.show', $a),
            ];
        });

        return response()->json($events);
    }
}
