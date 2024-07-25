<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|string|email|max:255|unique:users,email|exists:staffs,email',
            'password' => 'required|string|min:8|confirmed',
        ];
    }
    public function messages()
    {
        return [
            'email.unique' => 'Invalid email or password',
        ];
    }
}
