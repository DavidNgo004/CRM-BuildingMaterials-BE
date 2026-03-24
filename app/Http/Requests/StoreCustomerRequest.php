<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email',
            'phone' => 'required|digits:10|unique:customers,phone',
            'address' => 'nullable|string',
            'customer_type' => 'nullable|string|max:50|in:wholesale,retail',
            'status' => 'boolean',
            'notes' => 'nullable|string',
        ];
    }
}
