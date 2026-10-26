<?php

namespace App\Http\Requests;

use App\Enums\Modality;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isPatient() ?? false;
    }

    public function rules(): array
    {
        return [
            'professional_id' => ['required', 'integer', 'exists:professionals,id'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'time' => ['required', 'date_format:H:i'],
            'modality' => ['required', Rule::enum(Modality::class)],
            'reason' => ['nullable', 'string', 'max:1000'],
            'order_document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
            'booking_token' => ['nullable', 'string', 'max:64'],
            'authorization_document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'professional_id.required' => 'Debes seleccionar un profesional.',
            'date.required' => 'Debes seleccionar una fecha.',
            'time.required' => 'Debes seleccionar una hora.',
            'modality.required' => 'Debes seleccionar la modalidad.',
            'order_document.required' => 'Debes adjuntar la orden (PDF o imagen).',
            'order_document.mimes' => 'La orden debe ser PDF o imagen (JPG, PNG, WEBP).',
            'order_document.max' => 'La orden no puede superar 10 MB.',
            'authorization_document.required' => 'Debes adjuntar la autorización (PDF o imagen).',
            'authorization_document.mimes' => 'La autorización debe ser PDF o imagen (JPG, PNG, WEBP).',
            'authorization_document.max' => 'La autorización no puede superar 10 MB.',
        ];
    }
}
