<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Modality;
use App\Http\Controllers\Controller;
use App\Models\Professional;
use App\Models\WaitlistEntry;
use App\Services\AppointmentService;
use App\Services\AvailabilityService;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Admin\AssignWaitlistRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WaitlistController extends Controller
{
    public function __construct(
        private AvailabilityService $availability,
        private AppointmentService $appointmentService,
    ) {}

    public function index(Request $request): View
    {
        $query = WaitlistEntry::with(['patient', 'professional', 'assignedAppointment'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $entries = $query->paginate(20)->withQueryString();

        return view('admin.waitlist.index', compact('entries'));
    }

    public function updateStatus(Request $request, WaitlistEntry $waitlist): RedirectResponse
    {
        $request->validate(['status' => 'required|in:waiting,notified,assigned,cancelled']);
        $waitlist->update(['status' => $request->status]);

        return back()->with('success', 'Estado actualizado.');
    }

    /**
     * Formulario para asignar fecha/hora a una entrada de lista de espera.
     */
    public function assignForm(WaitlistEntry $waitlist): View
    {
        abort_unless(in_array($waitlist->status, ['waiting', 'notified'], true), 403, 'Esta solicitud ya no está pendiente.');

        $waitlist->load(['patient', 'professional']);
        $professionals = Professional::where('is_available', true)->orderBy('name')->get();

        $professional = $waitlist->professional
            ?? ($waitlist->specialty
                ? Professional::where('is_available', true)->where('specialty', $waitlist->specialty)->first()
                : null);

        $availableDates = $professional
            ? $this->availability->getAvailableDates($professional, 45)
            : collect();

        return view('admin.waitlist.assign', compact('waitlist', 'professionals', 'professional', 'availableDates'));
    }

    public function assign(AssignWaitlistRequest $request, WaitlistEntry $waitlist): RedirectResponse
    {
        abort_unless(in_array($waitlist->status, ['waiting', 'notified'], true), 403);

        $data = $request->validate([
            'professional_id' => ['required', 'exists:professionals,id'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'time' => ['required', 'date_format:H:i'],
            'modality' => ['required', 'in:presencial,virtual'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $professional = Professional::findOrFail($data['professional_id']);
        abort_unless($professional->is_available, 422, 'El profesional no está disponible.');

        $patient = $waitlist->patient;
        abort_unless($patient, 404);

        $appointment = $this->appointmentService->book(
            patient: $patient,
            professional: $professional,
            date: $data['date'],
            time: $data['time'],
            modality: Modality::from($data['modality']),
            reason: $data['reason'] ?? ('Asignada desde lista de espera #' . $waitlist->id),
            orderDocument: null,
            authorizationDocument: null,
        );

        $waitlist->update([
            'status' => 'assigned',
            'professional_id' => $professional->id,
            'assigned_at' => now(),
            'assigned_appointment_id' => $appointment->id,
        ]);

        return redirect()
            ->route('admin.waitlist.index')
            ->with('success', "Cita asignada a {$patient->name} el {$data['date']} a las {$data['time']}. La solicitud se retiró de la lista de espera.");
    }

    public function slots(Request $request)
    {
        $request->validate([
            'professional_id' => 'required|exists:professionals,id',
            'date' => 'required|date',
        ]);
        $professional = Professional::findOrFail($request->professional_id);
        $slots = $this->availability->getAvailableSlots($professional, \Carbon\Carbon::parse($request->date));

        return response()->json(['slots' => $slots]);
    }
}
