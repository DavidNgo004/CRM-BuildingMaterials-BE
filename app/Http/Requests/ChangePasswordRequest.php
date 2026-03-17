<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{

    public function rules()
    {
        return [

            'current_password'=>'required',
            'password'=>'required|min:6|confirmed'

        ];
    }

}