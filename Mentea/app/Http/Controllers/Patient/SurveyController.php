<?php

namespace App\Http\Controllers\Patient;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Survey;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SurveyController extends Controller
{
    public function create(Appointment $appointment): View|RedirectResponse
    {
        abort_unless($appointment->patient_id === Auth::id(), 403);
        abort_unless($appointment->status === AppointmentStatus::Completed, 403, 'Solo puedes evaluar citas completadas.');

        if ($appointment->survey) {
            return redirect()->route('patient.appointments.show', $appointment)
                ->with('info', 'Ya enviaste la encuesta de esta cita.');
        }

        return view('patient.surveys.create', compact('appointment'));
    }

    public function store(Request $request, Appointment $appointment): RedirectResponse
    {
        abort_unless($appointment->patient_id === Auth::id(), 403);
        abort_unless($appointment->status === AppointmentStatus::Completed, 403);

        if ($appointment->survey) {
            return redirect()->route('patient.appointments.show', $appointment);
        }

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        Survey::create([
            'appointment_id' => $appointment->id,
            'patient_id' => Auth::id(),
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
        ]);

        return redirect()
            ->route('patient.appointments.show', $appointment)
            ->with('success', '¡Gracias por tu valoración!');
    }
}
