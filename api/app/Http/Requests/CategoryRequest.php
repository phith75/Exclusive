<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
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
    public function rules()
    {
        $categoryId = $this->route('category');
        $method = $this->input('_method');
        $rules = [
            'name' => [
                'required',
                'max:255',
                'string',
                Rule::unique('categories')->ignore($categoryId)->whereNull('deleted_at'),
            ],
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];

        if ($method === 'PUT' || $method === 'PATCH') {
            $rules['image'] = 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        }

        return $rules;
    }
}
