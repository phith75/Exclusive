import apiRequest from "@/services/apiService";
import type { ApiResponse, ApiError } from "@/types/api-response";
import type {
  CategoryData,
  CreateCategoryData,
  UpdateCategoryData,
} from "@/types/index";

const categoryService = {
  async getCategories(
    page: number = 1,
    keywords: string = ""
  ): Promise<ApiResponse<CategoryData[]>> {
    return await apiRequest({
      method: "get",
      url: `/category`,
      params: { page, keywords },
    });
  },

  async createCategory(
    data: CreateCategoryData
  ): Promise<ApiResponse<CategoryData>> {
    const formData = new FormData();
    formData.append("name", data.name);
    if (data.image) {
      formData.append("image", data.image);
    }
    return await apiRequest({
      method: "post",
      url: `/category`,
      data: formData,
    });
  },

  async getCategoryById(
    categoryId: string
  ): Promise<ApiResponse<CategoryData>> {
    return await apiRequest({
      method: "get",
      url: `/category/${categoryId}`,
    });
  },

  async updateCategory(
    categoryId: string,
    data: UpdateCategoryData
  ): Promise<ApiResponse<UpdateCategoryData>> {
    const formData = new FormData();
    formData.append("name", data.name);
    if (data.image) {
      formData.append("image", data.image);
    }
    return await apiRequest({
      method: "post",
      url: `/category/${categoryId}?_method=PUT`,
      data: formData,
    });
  },

  async deleteCategory(categoryId: number): Promise<ApiResponse<null>> {
    return await apiRequest({
      method: "delete",
      url: `/category/${categoryId}`,
    });
  },
  // deleteMany

  async deleteManyCategories(
    categoryIds: number[]
  ): Promise<ApiResponse<null>> {
    return await apiRequest({
      method: "delete",
      url: `/deleteMany/category`,
      data: { ids: categoryIds },
    });
  },

  async getTrashed(
    page: number = 1,
    keywords: string = ""
  ): Promise<ApiResponse<CategoryData[]>> {
    try {
      return await apiRequest({
        method: "get",
        url: `/getTrashed/category`,
        params: { page, keywords },
      });
    } catch (error) {
      const apiError: ApiError = error as ApiError;
      throw new Error(`Failed to get trashed categories: ${apiError.message}`);
    }
  },

  async restore(categoryIds: number[] = []): Promise<ApiResponse<null>> {
    try {
      return await apiRequest({
        method: "put",
        url: `/restore/category`,
        data: { ids: categoryIds },
      });
    } catch (error) {
      const apiError: ApiError = error as ApiError;
      throw new Error(`Failed to restore categories: ${apiError.message}`);
    }
  },
};

export default categoryService;
