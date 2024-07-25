import apiRequest from "@/services/apiService";
import type { ApiResponse, ApiError } from "@/types/api-response";
import type { Category } from "@/types/index";

const categoryService = {
  async getCategories(): Promise<ApiResponse<Category[]>> {
    return await apiRequest({
      method: "get",
      url: `/getAll/categories`,
    });
  },

  async getCategoryById(categoryId: string): Promise<ApiResponse<Category>> {
    return await apiRequest({
      method: "get",
      url: `/category/${categoryId}`,
    });
  },
};

export default categoryService;
