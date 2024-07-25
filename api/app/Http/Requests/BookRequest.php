<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookRequest extends FormRequest
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
        $method = $this->input('_method');
        $rule = [
            'name' => 'required|max:255,string',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'quantity' => 'required|integer|min:0',
            'description' => 'required',
            'short_description' => 'required',
            'images' => 'required|array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'category_id' => 'required|exists:categories,id',
            'author_id' => 'required|exists:authors,id',
            'publisher_id' => 'required|exists:publishers,id',
        ];

        if ($method == 'PUT' || $method == 'PATCH') {
            $rule['thumbnail'] = 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048';
            $rule['images.*'] = 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048';
            $rule['images'] = 'nullable|array';
            $rule['old_images'] = 'nullable';
        }

        return $rule;
    }
}
