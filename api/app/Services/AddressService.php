<?php

namespace App\Services;

use App\Repositories\AddressRepository;

class AddressService
{
    protected $addressRepository;

    public function __construct(AddressRepository $addressRepository)
    {
        $this->addressRepository = $addressRepository;
    }

    public function getAllProvinces()
    {
        return $this->addressRepository->getProvinces();
    }

    public function getAllDistricts()
    {
        return $this->addressRepository->getAllDistricts();
    }

    public function getAllWards()
    {
        return $this->addressRepository->getAllWards();
    }

    public function getDistrictsByProvince($provinceId)
    {
        return $this->addressRepository->getDistrictsByProvinceId($provinceId);
    }

    public function getWardsByDistrict($districtId)
    {
        return $this->addressRepository->getWardsByDistrictId($districtId);
    }
}
