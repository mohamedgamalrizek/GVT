<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCrmClientUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('crmClient')) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['note', 'call', 'email', 'meeting', 'whatsapp', 'status_change'])],
            'body' => ['required', 'string', 'max:5000'],
            'status_to' => ['nullable', Rule::in(['new', 'qualified', 'contacted', 'proposal', 'negotiation', 'won', 'lost', 'on_hold'])],
            'contacted_at' => ['nullable', 'date'],
            'next_follow_up_at' => ['nullable', 'date'],
        ];
    }
}
