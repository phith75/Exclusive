import apiRequest from "@/services/apiService";
import type { ApiResponse } from "@/types/api-response";
import type { Publisher } from "@/types/index";

const publisherService = {
  async getPublishers(): Promise<ApiResponse<Publisher[]>> {
    return await apiRequest({
      method: "get",
      url: `/getAll/publishers`,
    });
  },

  async getPublisherById(publisherId: string): Promise<ApiResponse<Publisher>> {
    return await apiRequest({
      method: "get",
      url: `/publisher/${publisherId}`,
    });
  },
};

export default publisherService;
