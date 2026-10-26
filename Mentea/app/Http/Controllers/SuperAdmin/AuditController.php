<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditController extends Controller
{
    public function index(Request $request): View
    {
        $query = AuditLog::with('user')->latest();

        if ($action = $request->string('action')->toString()) {
            $query->where('action', 'like', '%' . $action . '%');
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }

        return view('super-admin.audit.index', [
            'logs' => $query->paginate(30)->withQueryString(),
            'action' => $action ?? '',
        ]);
    }

    public function show(AuditLog $audit): View
    {
        $audit->load('user');

        return view('super-admin.audit.show', ['log' => $audit]);
    }

    public function export(Request $request): StreamedResponse
    {
        $query = AuditLog::with('user')->latest();
        if ($action = $request->string('action')->toString()) {
            $query->where('action', 'like', '%' . $action . '%');
        }

        $filename = 'auditoria-mentea-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['id', 'fecha', 'accion', 'usuario', 'ip', 'old', 'new']);
            $query->limit(5000)->get()->each(function ($log) use ($out) {
                fputcsv($out, [
                    $log->id,
                    $log->created_at?->toDateTimeString(),
                    $log->action,
                    $log->user?->email,
                    $log->ip_address,
                    json_encode($log->old_values, JSON_UNESCAPED_UNICODE),
                    json_encode($log->new_values, JSON_UNESCAPED_UNICODE),
                ]);
            });
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
