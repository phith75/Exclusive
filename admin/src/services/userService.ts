import apiRequest from "@/services/apiService";
import type { ApiResponse } from "@/types/api-response";
import type {
  UserData,
  ImportStaffData,
  updateStatusUser,
  StaffList,
  updateRoleUser,
} from "@/types/index";

const userService = {
  async getUsers(
    page: number = 1,
    keywords: string = "",
    status: string = "",
    role: string = "",
    gender: string = ""
  ): Promise<ApiResponse<UserData[]>> {
    return await apiRequest({
      method: "get",
      url: `/user`,
      params: { page, keywords, role, status, gender },
    });
  },

  async importStaff(
    data: ImportStaffData
  ): Promise<ApiResponse<ImportStaffData>> {
    const formData = new FormData();
    formData.append("user_file", data.user_file);
    return await apiRequest({
      method: "post",
      url: `/import`,
      data: formData,
    });
  },
  async listStaff(): Promise<ApiResponse<StaffList[]>> {
    return await apiRequest({
      method: "get",
      url: `/staff-list`,
    });
  },
  async updateStatusUser(
    userId: string | number,
    data: updateStatusUser
  ): Promise<ApiResponse<updateStatusUser>> {
    return await apiRequest({
      method: "put",
      url: `/update-status-user/${userId}`,
      data: data,
    });
  },
  // update role
  async updateRoleUser(
    userId: string | number,
    data: updateRoleUser
  ): Promise<ApiResponse<updateStatusUser>> {
    return await apiRequest({
      method: "put",
      url: `/update-role-user/${userId}`,
      data: data,
    });
  },
};

export default userService;
