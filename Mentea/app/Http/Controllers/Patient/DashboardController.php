<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        if (auth()->user()->needsOnboarding()) {
            return redirect()->route('patient.onboarding.show');
        }

        $user = Auth::user();
        $upcoming = $user->appointmentsAsPatient()
            ->with('professional')
            ->whereDate('date', '>=', today())
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('date')
            ->orderBy('time')
            ->limit(5)
            ->get();

        return view('patient.dashboard', [
            'upcoming' => $upcoming,
        ]);
    }
}
