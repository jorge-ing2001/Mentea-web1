<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'lowercase', 'email', 'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'document_type' => ['nullable', 'string', 'in:CC,CE,TI,PA,RC,NIT,OTRO'],
            'document_number' => [
                'nullable', 'string', 'max:30',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'eps' => ['nullable', 'string', 'max:120'],
            'emergency_contact_name' => ['nullable', 'string', 'max:120'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:30'],
            'consent_informed' => ['nullable', 'boolean'],
            'consent_data' => ['nullable', 'boolean'],
        ];

        return $rules;
    }

    protected function passedValidation(): void
    {
        $user = $this->user();
        if ($this->boolean('consent_informed') && ! $user->consent_informed_at) {
            $this->merge(['consent_informed_at' => now()]);
        }
        if ($this->boolean('consent_data') && ! $user->consent_data_at) {
            $this->merge(['consent_data_at' => now()]);
        }
    }
}
