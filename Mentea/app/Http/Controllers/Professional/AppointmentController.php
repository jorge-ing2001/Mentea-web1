<?php

namespace App\Http\Controllers\Professional;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Services\AppointmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function __construct(private AppointmentService $appointmentService) {}

    private function professionalOrAbort()
    {
        $professional = Auth::user()->professionalProfile;
        abort_unless($professional, 403, 'Tu cuenta no está vinculada a un perfil profesional.');
        return $professional;
    }

    public function index(Request $request): View
    {
        $professional = $this->professionalOrAbort();

        $query = $professional->appointments()
            ->with('patient')
            ->orderByDesc('date')
            ->orderByDesc('time');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        $appointments = $query->paginate(20)->withQueryString();
        $statuses = AppointmentStatus::cases();

        return view('professional.appointments.index', compact('appointments', 'statuses', 'professional'));
    }

    public function show(Appointment $appointment): View
    {
        $professional = $this->professionalOrAbort();
        abort_unless($appointment->professional_id === $professional->id, 403);

        $appointment->load('patient');

        return view('professional.appointments.show', compact('appointment', 'professional'));
    }

    public function updateStatus(Request $request, Appointment $appointment): RedirectResponse
    {
        $professional = $this->professionalOrAbort();
        abort_unless($appointment->professional_id === $professional->id, 403);

        $request->validate([
            'status' => ['required', 'in:confirmed,completed,no_show'],
            'clinical_notes' => ['nullable', 'string', 'max:10000'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $status = AppointmentStatus::from($request->status);
        $actor = Auth::user();

        match ($status) {
            AppointmentStatus::Confirmed => $this->appointmentService->confirm($appointment, $actor),
            AppointmentStatus::Completed => $this->appointmentService->complete($appointment, $actor),
            AppointmentStatus::NoShow => $this->appointmentService->markNoShow($appointment, $actor),
        };

        if ($request->filled('clinical_notes')) {
            $appointment->update([
                'clinical_notes' => $request->string('clinical_notes')->toString(),
                'clinical_notes_updated_at' => now(),
                'clinical_notes_by' => $actor->id,
            ]);
        }

        if ($request->filled('notes')) {
            $appointment->update(['notes' => $request->notes]);
        }

        return redirect()
            ->route('professional.appointments.show', $appointment)
            ->with('success', 'Estado actualizado.');
    }
}
