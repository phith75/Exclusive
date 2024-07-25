<?php

namespace App\Repositories;

use App\Models\District;
use App\Models\Province;
use App\Models\Ward;

class AddressRepository
{
    public function getProvinces()
    {
        return Province::all();
    }

    public function getAllDistricts()
    {
        return District::all();
    }

    public function getAllWards()
    {
        return Ward::all();
    }

    public function getDistrictsByProvinceId($provinceId)
    {
        return Province::findOrFail($provinceId)->districts;
    }

    public function getWardsByDistrictId($districtId)
    {
        return District::findOrFail($districtId)->wards;
    }
}
