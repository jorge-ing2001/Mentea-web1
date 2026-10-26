<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreProfessionalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $id = $this->route('professional')?->id;

        return [
            'name' => ['required', 'string', 'max:150'],
            'specialty' => ['required', 'string', 'max:100'],
            'profession' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:2000'],
            'email' => ['required', 'email', 'max:150', 'unique:professionals,email,' . $id],
            'phone' => ['nullable', 'string', 'max:30'],
            'is_available' => ['sometimes', 'boolean'],
            'appointment_duration_minutes' => ['required', 'integer', 'min:15', 'max:180'],
        ];
    }
}
