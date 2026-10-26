<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAppointmentStatusRequest;
use App\Models\Appointment;
use App\Models\Professional;
use App\Services\AppointmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function __construct(private AppointmentService $appointmentService) {}

    public function index(Request $request): View
    {
        $query = Appointment::query()
            ->with(['patient', 'professional'])
            ->orderByDesc('date')
            ->orderByDesc('time');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('professional_id')) {
            $query->where('professional_id', $request->professional_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }
        if ($search = $request->query('q')) {
            $query->whereHas('patient', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('document_number', 'like', "%{$search}%");
            });
        }

        $appointments = $query->paginate(20)->withQueryString();
        $professionals = Professional::orderBy('name')->get(['id', 'name']);
        $statuses = AppointmentStatus::cases();

        return view('admin.appointments.index', compact('appointments', 'professionals', 'statuses'));
    }

    public function show(Appointment $appointment): View
    {
        $appointment->load(['patient', 'professional']);
        return view('admin.appointments.show', compact('appointment'));
    }

    public function updateStatus(UpdateAppointmentStatusRequest $request, Appointment $appointment): RedirectResponse
    {
        $status = AppointmentStatus::from($request->status);
        $actor = $request->user();

        match ($status) {
            AppointmentStatus::Confirmed => $this->appointmentService->confirm($appointment, $actor),
            AppointmentStatus::Completed => $this->appointmentService->complete($appointment, $actor),
            AppointmentStatus::NoShow => $this->appointmentService->markNoShow($appointment, $actor),
            AppointmentStatus::Cancelled => $this->appointmentService->cancelByAdmin(
                $appointment,
                $actor,
                $request->cancellation_reason
            ),
            default => $appointment->update([
                'status' => $status,
                'notes' => $request->notes ?? $appointment->notes,
            ]),
        };

        if ($request->filled('notes') && $status !== AppointmentStatus::Cancelled) {
            $appointment->update(['notes' => $request->notes]);
        }

        return redirect()
            ->back()
            ->with('success', 'Estado de la cita actualizado.');
    }
}
