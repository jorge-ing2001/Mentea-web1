<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function index(): View
    {
        return view('admin.calendar.index');
    }

    public function events(Request $request): JsonResponse
    {
        $start = $request->query('start', now()->startOfMonth()->toDateString());
        $end = $request->query('end', now()->endOfMonth()->toDateString());

        $appointments = Appointment::query()
            ->with(['patient', 'professional'])
            ->whereDate('date', '>=', $start)
            ->whereDate('date', '<=', $end)
            ->whereIn('status', [
                AppointmentStatus::Pending->value,
                AppointmentStatus::Confirmed->value,
                AppointmentStatus::Completed->value,
            ])
            ->get();

        $events = $appointments->map(function (Appointment $a) {
            $startDt = $a->date->format('Y-m-d') . 'T' . \Carbon\Carbon::parse($a->time)->format('H:i:s');
            $endDt = \Carbon\Carbon::parse($startDt)
                ->addMinutes($a->professional->appointment_duration_minutes ?? 45)
                ->format('Y-m-d\TH:i:s');

            $color = match ($a->status) {
                AppointmentStatus::Pending => '#f59e0b',
                AppointmentStatus::Confirmed => '#4f46e5',
                AppointmentStatus::Completed => '#16a34a',
                default => '#6b7280',
            };

            return [
                'id' => $a->id,
                'title' => ($a->patient->name ?? 'Paciente') . ' · ' . ($a->professional->name ?? ''),
                'start' => $startDt,
                'end' => $endDt,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'url' => route('admin.appointments.show', $a),
                'extendedProps' => [
                    'status' => $a->status->label(),
                    'modality' => $a->modality->label(),
                ],
            ];
        });

        return response()->json($events);
    }
}
