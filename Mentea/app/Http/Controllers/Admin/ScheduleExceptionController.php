<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Professional;
use App\Models\ScheduleException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScheduleExceptionController extends Controller
{
    public function index(Request $request): View
    {
        $query = ScheduleException::with('professional')->orderByDesc('date');

        if ($request->filled('professional_id')) {
            $query->where('professional_id', $request->integer('professional_id'));
        }
        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        return view('admin.exceptions.index', [
            'exceptions' => $query->paginate(20)->withQueryString(),
            'professionals' => Professional::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function create(): View
    {
        return view('admin.exceptions.create', [
            'professionals' => Professional::where('is_available', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'professional_id' => ['nullable', 'exists:professionals,id'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'time_from' => ['nullable', 'date_format:H:i'],
            'time_to' => ['nullable', 'date_format:H:i', 'after:time_from'],
            'type' => ['required', 'in:block,holiday,absence'],
            'reason' => ['nullable', 'string', 'max:255'],
            'full_day' => ['nullable', 'boolean'],
        ]);

        if ($request->boolean('full_day') || $data['type'] === 'holiday') {
            $data['time_from'] = null;
            $data['time_to'] = null;
        }
        if ($data['type'] === 'holiday') {
            $data['professional_id'] = null; // global
        }

        unset($data['full_day']);
        ScheduleException::create($data);

        return redirect()->route('admin.exceptions.index')
            ->with('success', 'Excepción de horario registrada. Los slots afectados ya no estarán disponibles.');
    }

    public function destroy(ScheduleException $exception): RedirectResponse
    {
        $exception->delete();

        return back()->with('success', 'Excepción eliminada.');
    }
}
