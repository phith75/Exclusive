<?php

namespace App\Http\Controllers\Api\v1\Client;

use App\Http\Controllers\Controller;
use App\Services\AddressService;
use Illuminate\Http\JsonResponse;

class AddressController extends Controller
{
    protected $addressService;

    public function __construct(AddressService $addressService)
    {
        $this->addressService = $addressService;
    }

    public function getProvinces(): JsonResponse
    {
        $provinces = $this->addressService->getAllProvinces();

        return $this->responseSuccess($provinces);
    }

    public function getDistricts($provinceId): JsonResponse
    {
        $districts = $this->addressService->getDistrictsByProvince($provinceId);

        return $this->responseSuccess($districts);
    }

    public function getWards($districtId): JsonResponse
    {
        $wards = $this->addressService->getWardsByDistrict($districtId);

        return $this->responseSuccess($wards);
    }

    public function getAllAddresses(): JsonResponse
    {
        $provinces = $this->addressService->getAllProvinces();
        $districts = $this->addressService->getAllDistricts();
        $wards = $this->addressService->getAllWards();

        $addresses = [
            'provinces' => $provinces,
            'districts' => $districts,
            'wards' => $wards,
        ];

        return $this->responseSuccess($addresses);
    }
}
