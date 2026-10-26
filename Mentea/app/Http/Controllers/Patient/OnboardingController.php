<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    public function show(): View|RedirectResponse
    {
        $user = Auth::user();
        if (! $user->needsOnboarding()) {
            return redirect()->route('patient.dashboard');
        }

        return view('patient.onboarding.show', ['user' => $user, 'step' => 1]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $data = $request->validate([
            'document_type' => ['required', 'in:CC,CE,TI,PA,RC,OTRO'],
            'document_number' => ['required', 'string', 'max:30'],
            'phone' => ['required', 'string', 'max:30'],
            'eps' => ['nullable', 'string', 'max:120'],
            'emergency_contact_name' => ['required', 'string', 'max:120'],
            'emergency_contact_phone' => ['required', 'string', 'max:30'],
            'consent_informed' => ['accepted'],
            'consent_data' => ['accepted'],
            'preferred_theme' => ['nullable', 'in:light,dark,system'],
        ]);

        $user->fill([
            'document_type' => $data['document_type'],
            'document_number' => $data['document_number'],
            'phone' => $data['phone'],
            'eps' => $data['eps'] ?? null,
            'emergency_contact_name' => $data['emergency_contact_name'],
            'emergency_contact_phone' => $data['emergency_contact_phone'],
            'preferred_theme' => $data['preferred_theme'] ?? 'system',
            'consent_informed_at' => $user->consent_informed_at ?? now(),
            'consent_data_at' => $user->consent_data_at ?? now(),
            'onboarding_completed_at' => now(),
        ])->save();

        return redirect()->route('patient.dashboard')
            ->with('success', 'Perfil completado. Ya puedes agendar citas.');
    }
}
