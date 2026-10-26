<?php

namespace App\Http\Controllers\Patient;

use App\Enums\Modality;
use App\Enums\WaitlistStatus;
use App\Http\Controllers\Controller;
use App\Models\Professional;
use App\Models\WaitlistEntry;
use App\Services\AppointmentService;
use App\Services\WaitlistService;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StoreWaitlistRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class WaitlistController extends Controller
{
    public function __construct(
        private WaitlistService $waitlistService,
        private AppointmentService $appointmentService,
    ) {}

    public function index(): View
    {
        $user = Auth::user();
        $active = $user->waitlistEntries()
            ->with(['professional', 'offeredProfessional'])
            ->whereIn('status', [WaitlistStatus::Waiting->value, WaitlistStatus::MatchFound->value])
            ->latest()
            ->get();

        $history = $user->waitlistEntries()
            ->with(['professional', 'offeredProfessional', 'assignedAppointment'])
            ->whereIn('status', [
                WaitlistStatus::Confirmed->value,
                WaitlistStatus::Expired->value,
                WaitlistStatus::Cancelled->value,
            ])
            ->latest()
            ->paginate(10, ['*'], 'historial');

        return view('patient.waitlist.index', compact('active', 'history'));
    }

    public function create(): View
    {
        $specialties = Professional::where('is_available', true)->distinct()->pluck('specialty')->sort()->values();
        $professionals = Professional::where('is_available', true)->orderBy('name')->get()
            ->groupBy('specialty');
        $modalities = Modality::cases();

        return view('patient.waitlist.create', compact('specialties', 'professionals', 'modalities'));
    }

    public function store(StoreWaitlistRequest $request): RedirectResponse
    {
        $data = $request->validate([
            'specialty' => ['required', 'string', 'max:100'],
            'professional_id' => ['nullable', 'exists:professionals,id'],
            'date_from' => ['required', 'date', 'after_or_equal:today'],
            'date_to' => ['required', 'date', 'after_or_equal:date_from'],
            'time_from' => ['nullable', 'date_format:H:i'],
            'time_to' => ['nullable', 'date_format:H:i', 'after:time_from'],
            'modality' => ['required', Rule::enum(Modality::class)],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'specialty.required' => 'Debes seleccionar una especialidad.',
            'date_from.required' => 'La fecha inicial es obligatoria.',
            'date_to.required' => 'La fecha final es obligatoria.',
            'date_to.after_or_equal' => 'La fecha final debe ser igual o posterior a la inicial.',
            'modality.required' => 'Debes seleccionar la modalidad.',
        ]);

        if (! empty($data['professional_id'])) {
            $pro = Professional::find($data['professional_id']);
            if ($pro && strcasecmp($pro->specialty, $data['specialty']) !== 0) {
                return back()->withErrors(['professional_id' => 'El profesional no pertenece a la especialidad seleccionada.'])->withInput();
            }
        }

        WaitlistEntry::create([
            'patient_id' => Auth::id(),
            'specialty' => $data['specialty'],
            'professional_id' => $data['professional_id'] ?? null,
            'date_from' => $data['date_from'],
            'date_to' => $data['date_to'],
            'time_from' => $data['time_from'] ?? null,
            'time_to' => $data['time_to'] ?? null,
            'modality' => Modality::from($data['modality']),
            'notes' => $data['notes'] ?? null,
            'status' => WaitlistStatus::Waiting,
        ]);

        return redirect()
            ->route('patient.waitlist.index')
            ->with('success', 'Solicitud registrada. Te avisaremos cuando haya una disponibilidad compatible.');
    }

    public function show(WaitlistEntry $waitlist): View
    {
        abort_unless($waitlist->patient_id === Auth::id(), 403);
        $waitlist->load(['professional', 'offeredProfessional', 'assignedAppointment']);

        return view('patient.waitlist.show', compact('waitlist'));
    }

    public function confirm(WaitlistEntry $waitlist): RedirectResponse
    {
        abort_unless($waitlist->patient_id === Auth::id(), 403);

        try {
            $appointment = $this->waitlistService->confirmOffer($waitlist, $this->appointmentService);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('patient.waitlist.show', $waitlist)
                ->withErrors($e->errors());
        }

        return redirect()
            ->route('patient.appointments.show', $appointment)
            ->with('success', '¡Cita confirmada desde la lista de espera!');
    }

    public function reject(WaitlistEntry $waitlist): RedirectResponse
    {
        abort_unless($waitlist->patient_id === Auth::id(), 403);
        $this->waitlistService->rejectOffer($waitlist);

        return redirect()
            ->route('patient.waitlist.index')
            ->with('success', 'Disponibilidad rechazada. Seguiremos buscando si tu solicitud sigue activa.');
    }

    public function cancel(WaitlistEntry $waitlist): RedirectResponse
    {
        abort_unless($waitlist->patient_id === Auth::id(), 403);

        if (! in_array($waitlist->status, [WaitlistStatus::Waiting, WaitlistStatus::MatchFound], true)) {
            return back()->withErrors(['status' => 'Esta solicitud ya no se puede cancelar.']);
        }

        $this->waitlistService->cancelRequest($waitlist);

        return redirect()
            ->route('patient.waitlist.index')
            ->with('success', 'Has salido de la lista de espera. El registro se conserva en tu historial.');
    }
}
