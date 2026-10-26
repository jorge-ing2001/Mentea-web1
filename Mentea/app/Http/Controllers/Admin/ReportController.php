<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Professional;
use App\Models\Survey;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $from = Carbon::parse($request->input('from', now()->startOfMonth()->toDateString()));
        $to = Carbon::parse($request->input('to', now()->endOfMonth()->toDateString()));

        $byMonth = Appointment::query()
            ->selectRaw("strftime('%Y-%m', date) as ym, count(*) as total")
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->groupBy('ym')
            ->orderBy('ym')
            ->pluck('total', 'ym');

        // MySQL compatible alternative fallback if sqlite fails - use collection
        if ($byMonth->isEmpty()) {
            $byMonth = Appointment::query()
                ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
                ->get()
                ->groupBy(fn ($a) => $a->date->format('Y-m'))
                ->map->count();
        }

        $byProfessional = Appointment::query()
            ->with('professional')
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->get()
            ->groupBy('professional_id')
            ->map(function ($items, $id) {
                return [
                    'name' => $items->first()->professional?->name ?? '—',
                    'total' => $items->count(),
                    'completed' => $items->where('status', AppointmentStatus::Completed)->count(),
                    'no_show' => $items->where('status', AppointmentStatus::NoShow)->count(),
                ];
            })
            ->values();

        $nps = [
            'count' => Survey::whereBetween('created_at', [$from, $to->copy()->endOfDay()])->count(),
            'avg' => round((float) Survey::whereBetween('created_at', [$from, $to->copy()->endOfDay()])->avg('rating'), 2),
        ];

        $atRisk = User::query()
            ->where('role', 'patient')
            ->where(function ($q) {
                $q->where('no_show_count', '>=', 2)
                    ->orWhere('cancellation_count', '>=', 3)
                    ->orWhereNotNull('booking_blocked_until');
            })
            ->orderByDesc('no_show_count')
            ->limit(30)
            ->get();

        return view('admin.reports.index', compact('from', 'to', 'byMonth', 'byProfessional', 'nps', 'atRisk'));
    }

    public function export(Request $request): StreamedResponse
    {
        $from = Carbon::parse($request->input('from', now()->startOfMonth()->toDateString()));
        $to = Carbon::parse($request->input('to', now()->endOfMonth()->toDateString()));

        $rows = Appointment::with(['patient', 'professional'])
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->orderBy('date')
            ->get();

        $filename = 'reporte-citas-' . $from->format('Ymd') . '-' . $to->format('Ymd') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['id', 'fecha', 'hora', 'paciente', 'profesional', 'estado', 'modalidad']);
            foreach ($rows as $a) {
                fputcsv($out, [
                    $a->id,
                    $a->date?->format('Y-m-d'),
                    \Carbon\Carbon::parse($a->time)->format('H:i'),
                    $a->patient?->name,
                    $a->professional?->name,
                    $a->status?->value,
                    $a->modality?->value,
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
