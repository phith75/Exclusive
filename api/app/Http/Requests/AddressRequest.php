<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    public function authorize()
    {
        // Tùy chỉnh logic để cho phép hoặc từ chối request
        return true;
    }

    public function rules()
    {
        $routeName = $this->route()->getName();

        switch ($routeName) {
            case 'getDistricts':
                return [
                    'provinceId' => 'required|integer|exists:provinces,id',
                ];
            case 'getWards':
                return [
                    'districtId' => 'required|integer|exists:districts,id',
                ];
            default:
                return [];
        }
    }
}
