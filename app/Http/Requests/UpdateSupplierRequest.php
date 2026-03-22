<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('supplier') ?? $this->route('id');
        return [
            'name' => 'required|string|max:255',
            'tax_code' => 'nullable|string|max:50',
            'phone' => 'required|digits:10|unique:suppliers,phone,' . $id,
            'email' => 'required|email|max:100|unique:suppliers,email,' . $id,
            'address' => 'nullable|string',
            'status' => 'boolean',
            'notes' => 'nullable|string',
        ];
    }
}
