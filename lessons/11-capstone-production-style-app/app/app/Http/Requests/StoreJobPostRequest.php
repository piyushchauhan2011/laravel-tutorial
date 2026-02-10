<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:120'],
            'department' => ['required', 'string', 'max:80'],
            'location' => ['required', 'string', 'max:80'],
            'employment_type' => ['required', 'in:full_time,contract,internship'],
            'status' => ['required', 'in:draft,open,closed'],
            'is_remote' => ['sometimes', 'boolean'],
            'salary_min' => ['nullable', 'integer', 'min:0'],
            'salary_max' => ['nullable', 'integer', 'min:0', 'gte:salary_min'],
            'description' => ['required', 'string', 'min:20'],
        ];
    }
}
