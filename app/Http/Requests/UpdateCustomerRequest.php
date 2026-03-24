<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('customer') ?? $this->route('id');
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email,' . $id,
            'phone' => 'required|digits:10|unique:customers,phone,' . $id,
            'address' => 'nullable|string',
            'customer_type' => 'nullable|string|max:50|in:wholesale,retail',
            'status' => 'boolean',
            'notes' => 'nullable|string',
        ];
    }
}
