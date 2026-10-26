<?php

namespace App\Http\Requests;

use App\Enums\Modality;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWaitlistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isPatient() ?? false;
    }

    public function rules(): array
    {
        return [
            'specialty' => ['required', 'string', 'max:120'],
            'professional_id' => ['nullable', 'exists:professionals,id'],
            'date_from' => ['required', 'date', 'after_or_equal:today'],
            'date_to' => ['required', 'date', 'after_or_equal:date_from'],
            'time_from' => ['nullable', 'date_format:H:i'],
            'time_to' => ['nullable', 'date_format:H:i'],
            'modality' => ['nullable', Rule::enum(Modality::class)],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'specialty.required' => 'Selecciona una especialidad.',
            'date_from.required' => 'Indica la fecha inicial.',
            'date_to.after_or_equal' => 'La fecha final debe ser posterior o igual a la inicial.',
        ];
    }
}
