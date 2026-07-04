<?php

namespace App\Http\Requests\Admin;

use App\Models\CrmClient;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCrmClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', CrmClient::class) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'company' => ['nullable', 'string', 'max:255'],
            'investor_type' => ['nullable', 'string', 'max:100'],
            'status' => ['required', Rule::in(['new', 'qualified', 'contacted', 'proposal', 'negotiation', 'won', 'lost', 'on_hold'])],
            'priority' => ['required', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'source' => ['nullable', 'string', 'max:100'],
            'budget_range' => ['nullable', 'string', 'max:100'],
            'preferred_market' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'assigned_to_user_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'next_follow_up_at' => ['nullable', 'date'],
        ];
    }
}
