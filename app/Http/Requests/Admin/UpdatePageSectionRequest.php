<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePageSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage pages') === true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'eyebrow' => ['nullable', 'string', 'max:160'],
            'heading' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string', 'max:3000'],
            'content' => ['nullable', 'json'],
            'content_payload' => ['nullable', 'array'],
        ];
    }
}
