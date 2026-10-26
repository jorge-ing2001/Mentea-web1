<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AuditLog;
use App\Support\MenteaLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function view(Appointment $appointment, string $type)
    {
        $this->authorizeDocumentAccess($appointment);
        $this->auditAccess($appointment, $type, 'view');
        $path = $this->resolvePath($appointment, $type);
        $mime = Storage::disk('local')->mimeType($path) ?: 'application/octet-stream';

        return response()->file(Storage::disk('local')->path($path), [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
        ]);
    }

    public function download(Appointment $appointment, string $type): StreamedResponse
    {
        $this->authorizeDocumentAccess($appointment);
        $this->auditAccess($appointment, $type, 'download');
        $path = $this->resolvePath($appointment, $type);
        $label = $type === 'order' ? 'orden' : 'autorizacion';

        return Storage::disk('local')->download(
            $path,
            $label . '_cita_' . $appointment->id . '.' . pathinfo($path, PATHINFO_EXTENSION)
        );
    }

    private function resolvePath(Appointment $appointment, string $type): string
    {
        abort_unless(in_array($type, ['order', 'authorization'], true), 404);

        $path = $type === 'order'
            ? $appointment->order_document
            : $appointment->authorization_document;

        abort_unless($path && Storage::disk('local')->exists($path), 404, 'Documento no encontrado.');

        return $path;
    }

    private function authorizeDocumentAccess(Appointment $appointment): void
    {
        $user = Auth::user();
        abort_unless($user, 403);

        if ($user->isSuperAdmin() || $user->isAdmin()) {
            return;
        }
        if ($user->isPatient() && $appointment->patient_id === $user->id) {
            return;
        }
        if ($user->isProfessional()) {
            $appointment->loadMissing('professional');
            if ($appointment->professional?->user_id === $user->id) {
                return;
            }
        }

        abort(403, 'No tienes permiso para ver este documento.');
    }

    private function auditAccess(Appointment $appointment, string $type, string $action): void
    {
        try {
            MenteaLog::security('document_access', [
                'user_id' => Auth::id(),
                'appointment_id' => $appointment->id,
                'type' => $type,
                'action' => $action,
            ]);
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => "document.{$action}.{$type}",
                'auditable_type' => Appointment::class,
                'auditable_id' => $appointment->id,
                'old_values' => null,
                'new_values' => ['type' => $type, 'action' => $action],
                'ip_address' => request()->ip(),
                'user_agent' => substr((string) request()->userAgent(), 0, 500),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
