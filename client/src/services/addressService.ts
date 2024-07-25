import apiRequest from "./apiService";
import type { ProvinceData, DistrictData, WardData } from "@/types";

const addressService = {
  async getProvinces(): Promise<ProvinceData[]> {
    try {
      const response = await apiRequest({
        method: "get",
        url: "/provinces",
      });
      return response.data;
    } catch (error) {
      console.error("Error fetching provinces:", error);
      throw error;
    }
  },

  async getDistricts(provinceId: number): Promise<DistrictData[]> {
    try {
      const response = await apiRequest({
        method: "get",
        url: `/districts/${provinceId}`,
      });
      return response.data;
    } catch (error) {
      console.error("Error fetching districts:", error);
      throw error;
    }
  },

  async getWards(districtId: number): Promise<WardData[]> {
    try {
      const response = await apiRequest({
        method: "get",
        url: `/wards/${districtId}`,
      });
      return response.data;
    } catch (error) {
      console.error("Error fetching wards:", error);
      throw error;
    }
  },
  async getAddresses(): Promise<AddressData[]> {
    try {
      const response = await apiRequest({
        method: "get",
        url: "/addresses",
      });
      return response.data;
    } catch (error) {
      console.error("Error fetching addresses:", error);
      throw error;
    }
  },
};

export default addressService;
