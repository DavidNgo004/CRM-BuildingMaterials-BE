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
            'code' => 'nullable|string|max:50|unique:suppliers,code,' . $id,
            'name' => 'required|string|max:255',
            'tax_code' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'status' => 'boolean',
            'notes' => 'nullable|string',
        ];
    }
}
