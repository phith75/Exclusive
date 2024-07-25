import apiRequest from "@/services/apiService";
import type { ApiResponse } from "@/types/api-response";
import type { Author } from "@/types/index";

const authorService = {
  async getAuthors(): Promise<ApiResponse<Author[]>> {
    return await apiRequest({
      method: "get",
      url: `/getAll/authors`,
    });
  },

  async getAuthorById(authorId: string): Promise<ApiResponse<Author>> {
    return await apiRequest({
      method: "get",
      url: `/author/${authorId}`,
    });
  },
};

export default authorService;
