<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function index(): View
    {
        return view('patient.calendar.index');
    }

    public function events(Request $request)
    {
        $start = $request->string('start')->toString();
        $end = $request->string('end')->toString();

        $query = Appointment::query()
            ->with('professional')
            ->where('patient_id', Auth::id());

        if ($start) {
            $query->whereDate('date', '>=', substr($start, 0, 10));
        }
        if ($end) {
            $query->whereDate('date', '<=', substr($end, 0, 10));
        }

        $events = $query->get()->map(function (Appointment $a) {
            $time = $a->time instanceof \Carbon\Carbon
                ? $a->time->format('H:i:s')
                : substr((string) $a->time, 0, 8);

            return [
                'id' => $a->id,
                'title' => ($a->professional?->name ?? 'Cita') . ' · ' . ($a->status?->label() ?? ''),
                'start' => $a->date->format('Y-m-d') . 'T' . $time,
                'url' => route('patient.appointments.show', $a),
                'backgroundColor' => match ($a->status?->value) {
                    'confirmed' => '#0d9488',
                    'pending' => '#f59e0b',
                    'cancelled' => '#ef4444',
                    'completed' => '#64748b',
                    'no_show' => '#94a3b8',
                    default => '#14b8a6',
                },
            ];
        });

        return response()->json($events);
    }
}
