<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RescheduleAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'date' => ['required', 'date', 'after_or_equal:today'],
            'time' => ['required', 'date_format:H:i'],
            'professional_id' => ['nullable', 'exists:professionals,id'],
            'modality' => ['nullable', 'in:in_person,virtual'],
        ];
    }

    public function messages(): array
    {
        return [
            'date.required' => 'Selecciona la nueva fecha.',
            'time.required' => 'Selecciona la nueva hora.',
        ];
    }
}
