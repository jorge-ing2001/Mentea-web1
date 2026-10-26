<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProfessionalRequest;
use App\Models\Professional;
use App\Models\Schedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfessionalController extends Controller
{
    public function index(Request $request): View
    {
        $query = Professional::query()->withCount('appointments');

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('specialty', 'like', "%{$search}%");
            });
        }

        if ($request->filled('specialty')) {
            $query->where('specialty', $request->specialty);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_available', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_available', false);
            }
        }

        $professionals = $query->orderBy('name')->paginate(15)->withQueryString();
        $specialties = Professional::query()->distinct()->pluck('specialty')->sort()->values();

        return view('admin.professionals.index', compact('professionals', 'specialties'));
    }

    public function create(): View
    {
        return view('admin.professionals.create');
    }

    public function store(StoreProfessionalRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_available'] = $request->boolean('is_available', true);

        $professional = Professional::create($data);

        foreach ([1, 2, 3, 4, 5] as $day) {
            Schedule::create([
                'professional_id' => $professional->id,
                'day_of_week' => $day,
                'start_time' => '08:00',
                'end_time' => '12:00',
                'is_active' => true,
            ]);
            Schedule::create([
                'professional_id' => $professional->id,
                'day_of_week' => $day,
                'start_time' => '14:00',
                'end_time' => '18:00',
                'is_active' => true,
            ]);
        }

        return redirect()
            ->route('admin.professionals.index')
            ->with('success', 'Profesional creado correctamente.');
    }

    public function edit(Professional $professional): View
    {
        $professional->load('schedules');
        return view('admin.professionals.edit', compact('professional'));
    }

    public function update(StoreProfessionalRequest $request, Professional $professional): RedirectResponse
    {
        $data = $request->validated();
        $data['is_available'] = $request->boolean('is_available', $professional->is_available);
        $professional->update($data);

        return redirect()
            ->route('admin.professionals.index')
            ->with('success', 'Profesional actualizado.');
    }

    /**
     * Activar / inactivar (reemplaza destroy).
     */
    public function toggleAvailability(Professional $professional): RedirectResponse
    {
        $professional->update([
            'is_available' => ! $professional->is_available,
        ]);

        $label = $professional->is_available ? 'activado' : 'inactivado';

        return back()->with('success', "Profesional {$label} correctamente. " .
            ($professional->is_available
                ? 'Volverá a aparecer en el agendamiento.'
                : 'Ya no aparecerá como opción para nuevas citas. Su historial se conserva.'));
    }
}
