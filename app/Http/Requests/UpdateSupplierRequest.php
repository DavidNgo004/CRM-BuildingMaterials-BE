<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $supplier = $this->route('supplier');
        $id = is_object($supplier) ? $supplier->id : $supplier;

        return [
            'name' => 'required|string|max:255',
            'tax_code' => 'nullable|string|max:50',
            'phone' => [
                'required',
                'digits:10',
                Rule::unique('suppliers', 'phone')->ignore($id),
            ],
            'email' => [
                'required',
                'email',
                'max:100',
                Rule::unique('suppliers', 'email')->ignore($id),
            ],
            'address' => 'nullable|string',
            'status' => 'boolean',
            'notes' => 'nullable|string',
        ];
    }
}
