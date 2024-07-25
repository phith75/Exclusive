<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user');

        $rules = [
            'name' => 'required|max:255|string',
            'user_code' => 'required|max:10|string|unique:users,user_code,' . $userId,
            'email' => 'required|max:255|email|unique:users,email,' . $userId,
            'address' => 'required|max:255|string',
            'phone' => 'nullable|max:16|string|min:10|regex:/^([0-9\s\-\+\(\)]*)$/|unique:users,phone,' . $userId,
            'gender' => 'required|in:0,1,2',
        ];
        if (! $this->has('google_id')) {
            $rules['password'] = 'required|max:255|string';
        } else {
            $rules['password'] = 'nullable|max:255|string';
        }

        return $rules;
    }
}
