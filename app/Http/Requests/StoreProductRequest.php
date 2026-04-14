<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest{
    public function authorize(): bool { return true; }
    
    public function rules(){
        return[
            'supplier_id' => 'required|exists:suppliers,id',
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'import_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'reorder_level' => 'nullable|numeric|min:0',
            'status' => 'boolean',
        ];
    }
}