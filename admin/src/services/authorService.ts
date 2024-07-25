import apiRequest from "@/services/apiService";
import type { ApiResponse, ApiError } from "@/types/api-response";
import type {
  AuthorData,
  CreateAuthorData,
  UpdateAuthorData,
} from "@/types/index";

const authorService = {
  async getAuthors(
    page: number = 1,
    keywords: string = ""
  ): Promise<ApiResponse<AuthorData[]>> {
    return await apiRequest({
      method: "get",
      url: `/author`,
      params: { page, keywords },
    });
  },

  async createAuthor(data: CreateAuthorData): Promise<ApiResponse<AuthorData>> {
    return await apiRequest({
      method: "post",
      url: `/author`,
      data,
    });
  },

  async getAuthorById(authorId: string): Promise<ApiResponse<AuthorData>> {
    return await apiRequest({
      method: "get",
      url: `/author/${authorId}`,
    });
  },

  async updateAuthor(
    authorId: string,
    data: UpdateAuthorData
  ): Promise<ApiResponse<UpdateAuthorData>> {
    return await apiRequest({
      method: "put",
      url: `/author/${authorId}`,
      data,
    });
  },

  async deleteAuthor(authorId: number): Promise<ApiResponse<null>> {
    return await apiRequest({
      method: "delete",
      url: `/author/${authorId}`,
    });
  },

  async deleteManyAuthors(authorIds: number[]): Promise<ApiResponse<null>> {
    return await apiRequest({
      method: "delete",
      url: `/deleteMany/author`,
      data: { ids: authorIds },
    });
  },

  async getTrashed(
    page: number = 1,
    keywords: string = ""
  ): Promise<ApiResponse<AuthorData[]>> {
    try {
      return await apiRequest({
        method: "get",
        url: `/getTrashed/author`,
        params: { page, keywords },
      });
    } catch (error) {
      const apiError: ApiError = error as ApiError;
      throw new Error(`Failed to get trashed authors: ${apiError.message}`);
    }
  },

  async restore(authorIds: number[] = []): Promise<ApiResponse<null>> {
    try {
      return await apiRequest({
        method: "put",
        url: `/restore/author`,
        data: { ids: authorIds },
      });
    } catch (error) {
      const apiError: ApiError = error as ApiError;
      throw new Error(`Failed to restore authors: ${apiError.message}`);
    }
  },
};

export default authorService;
