<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'candidate_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email'],
            'source' => ['required', 'in:career_site,referral,agency,linkedin'],
            'years_experience' => ['required', 'integer', 'min:0', 'max:50'],
            'cover_letter' => ['nullable', 'string'],
            'resume_text' => ['required', 'string', 'min:30'],
            'referred_by_application_id' => ['nullable', 'integer', 'exists:applications,id'],
        ];
    }
}
