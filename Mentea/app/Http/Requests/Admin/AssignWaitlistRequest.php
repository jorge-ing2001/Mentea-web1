<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AssignWaitlistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() || $this->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'professional_id' => ['required', 'exists:professionals,id'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'time' => ['required', 'date_format:H:i'],
            'modality' => ['required', 'in:in_person,virtual'],
        ];
    }
}
