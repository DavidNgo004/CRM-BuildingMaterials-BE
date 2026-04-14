<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $customer = $this->route('customer');
        $id = is_object($customer) ? $customer->id : $customer;

        return [
            'name' => 'required|string|max:255',
            'email' => [
                'nullable',
                'email',
                Rule::unique('customers', 'email')->ignore($id),
            ],
            'phone' => [
                'required',
                'digits:10',
                Rule::unique('customers', 'phone')->ignore($id),
            ],
            'address' => 'nullable|string',
            'customer_type' => 'nullable|string|max:50|in:wholesale,retail',
            'status' => 'boolean',
            'notes' => 'nullable|string',
        ];
    }
}
