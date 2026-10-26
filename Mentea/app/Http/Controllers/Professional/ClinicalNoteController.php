<?php

namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\ClinicalEvolution;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClinicalNoteController extends Controller
{
    public function store(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->authorize('updateClinicalNotes', $appointment);

        $data = $request->validate([
            'content' => ['required', 'string', 'max:10000'],
            'diagnosis_code' => ['nullable', 'string', 'max:20'],
            'diagnosis_label' => ['nullable', 'string', 'max:255'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);

        $path = null;
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store(
                'clinical/' . $appointment->id,
                'local'
            );
        }

        ClinicalEvolution::create([
            'appointment_id' => $appointment->id,
            'author_id' => Auth::id(),
            'content' => $data['content'],
            'attachment_path' => $path,
        ]);

        if (! empty($data['diagnosis_code']) || ! empty($data['diagnosis_label'])) {
            $appointment->update([
                'diagnosis_code' => $data['diagnosis_code'] ?? $appointment->diagnosis_code,
                'diagnosis_label' => $data['diagnosis_label'] ?? $appointment->diagnosis_label,
                'clinical_notes' => $data['content'],
                'clinical_notes_updated_at' => now(),
                'clinical_notes_by' => Auth::id(),
            ]);
        } else {
            $appointment->update([
                'clinical_notes' => $data['content'],
                'clinical_notes_updated_at' => now(),
                'clinical_notes_by' => Auth::id(),
            ]);
        }

        return back()->with('success', 'Evolución clínica guardada.');
    }
}
