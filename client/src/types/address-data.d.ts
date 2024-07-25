export interface ProvinceData {
  id: number;
  name: string;
}

export interface DistrictData {
  id: number;
  name: string;
  province_id: number;
}

export interface WardData {
  id: number;
  name: string;
  district_id: number;
}

export interface AddressData {
  province: ProvinceData;
  district: DistrictData;
  ward: WardData;
}
