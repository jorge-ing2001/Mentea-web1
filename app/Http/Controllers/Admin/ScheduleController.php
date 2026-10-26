<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Professional;
use App\Models\Schedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function edit(Professional $professional): View
    {
        $professional->load('schedules');
        $days = [
            1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles',
            4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo',
        ];

        return view('admin.schedules.edit', compact('professional', 'days'));
    }

    public function update(Request $request, Professional $professional): RedirectResponse
    {
        $request->validate([
            'schedules' => ['required', 'array'],
            'schedules.*.day_of_week' => ['required', 'integer', 'between:1,7'],
            'schedules.*.start_time' => ['required', 'date_format:H:i'],
            'schedules.*.end_time' => ['required', 'date_format:H:i', 'after:schedules.*.start_time'],
            'schedules.*.is_active' => ['sometimes', 'boolean'],
        ]);

        $professional->schedules()->delete();

        foreach ($request->schedules as $row) {
            if (empty($row['start_time']) || empty($row['end_time'])) {
                continue;
            }
            Schedule::create([
                'professional_id' => $professional->id,
                'day_of_week' => $row['day_of_week'],
                'start_time' => $row['start_time'],
                'end_time' => $row['end_time'],
                'is_active' => ! empty($row['is_active']),
            ]);
        }

        return redirect()
            ->route('admin.professionals.edit', $professional)
            ->with('success', 'Horarios actualizados.');
    }
}
