import apiRequest from "@/services/apiService";
import type { ApiResponse, ApiError } from "@/types/api-response";
import type {
  PublisherData,
  CreatePublisherData,
  UpdatePublisherData,
} from "@/types/index";

const publisherService = {
  async getPublishers(
    page: number = 1,
    keywords: string = ""
  ): Promise<ApiResponse<PublisherData[]>> {
    return await apiRequest({
      method: "get",
      url: `/publisher`,
      params: { page, keywords },
    });
  },

  async createPublisher(
    data: CreatePublisherData
  ): Promise<ApiResponse<PublisherData>> {
    return await apiRequest({
      method: "post",
      url: `/publisher`,
      data,
    });
  },

  async getPublisherById(
    publisherId: string
  ): Promise<ApiResponse<PublisherData>> {
    return await apiRequest({
      method: "get",
      url: `/publisher/${publisherId}`,
    });
  },

  async updatePublisher(
    publisherId: string,
    data: UpdatePublisherData
  ): Promise<ApiResponse<UpdatePublisherData>> {
    return await apiRequest({
      method: "put",
      url: `/publisher/${publisherId}`,
      data,
    });
  },

  async deletePublisher(publisherId: number): Promise<ApiResponse<null>> {
    return await apiRequest({
      method: "delete",
      url: `/publisher/${publisherId}`,
    });
  },

  async deleteManyPublishers(
    publisherIds: number[]
  ): Promise<ApiResponse<null>> {
    return await apiRequest({
      method: "delete",
      url: `/deleteMany/publisher`,
      data: { ids: publisherIds },
    });
  },

  async getTrashed(
    page: number = 1,
    keywords: string = ""
  ): Promise<ApiResponse<PublisherData[]>> {
    try {
      return await apiRequest({
        method: "get",
        url: `/getTrashed/publisher`,
        params: { page, keywords },
      });
    } catch (error) {
      const apiError: ApiError = error as ApiError;
      throw new Error(`Failed to get trashed publishers: ${apiError.message}`);
    }
  },

  async restore(publisherIds: number[] = []): Promise<ApiResponse<null>> {
    try {
      return await apiRequest({
        method: "put",
        url: `/restore/publisher`,
        data: { ids: publisherIds },
      });
    } catch (error) {
      const apiError: ApiError = error as ApiError;
      throw new Error(`Failed to restore publishers: ${apiError.message}`);
    }
  },
};

export default publisherService;
