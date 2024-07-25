<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TicketDetailRequest extends FormRequest
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
            'quantity' => 'nullable|numeric',
            'book_id' => 'required|numeric',
            'book_name' => 'required|string',
            'expected_refund_time' => 'required|date',

        ];
    }
}
