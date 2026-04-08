<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportExcelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // middleware handle role
    }

    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:xlsx,xls|max:10240'
        ];
    }

    public function messages()
    {
        return [
            'file.required' => 'File Excel không được để trống',
            'file.mimes' => 'File phải là Excel (.xlsx hoặc .xls)'
        ];
    }
}