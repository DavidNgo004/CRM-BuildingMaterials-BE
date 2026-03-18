<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class StoreProductRequest extends FormRequest{
    public function rules(){
        return[
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'import_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
        ];
    }
}