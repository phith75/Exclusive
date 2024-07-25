<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LendTicketRequest extends FormRequest
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
        return [
            'user_id' => 'required|numeric',
            'note' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
            'book_data' => 'required|array',
            'phone' => 'nullable|string|min:10|max:16',
        ];
    }
}
