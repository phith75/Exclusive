import apiRequest from "@/services/apiService";
import type { ApiResponse, ApiError } from "@/types/api-response";
import type {
  ReviewData,
  CreateReviewData,
  UpdateStatusReviewData,
} from "@/types/index";

const reviewService = {
  async getReviews(
    page: number = 1,
    keywords: string = "",
    status: string = "",
    rating: string = ""
  ): Promise<ApiResponse<ReviewData[]>> {
    return await apiRequest({
      method: "get",
      url: `/review`,
      params: { page, keywords, status, rating },
    });
  },

  async createReview(data: CreateReviewData): Promise<ApiResponse<ReviewData>> {
    return await apiRequest({
      method: "post",
      url: `/review`,
      data,
    });
  },

  async updateStatusReview(
    reviewId: number,
    data: UpdateStatusReviewData
  ): Promise<ApiResponse<UpdateStatusReviewData>> {
    return await apiRequest({
      method: "put",
      url: `/update-status-review/${reviewId}`,
      data,
    });
  },

  async deleteReview(reviewId: number): Promise<ApiResponse<null>> {
    try {
      return await apiRequest({
        method: "delete",
        url: `/review/${reviewId}`,
      });
    } catch (error) {
      const apiError: ApiError = error as ApiError;
      throw new Error(`Failed to delete review: ${apiError.message}`);
    }
  },

  async deleteManyReviews(reviewIds: number[]): Promise<ApiResponse<null>> {
    try {
      return await apiRequest({
        method: "delete",
        url: `/deleteMany/review`,
        data: { ids: reviewIds },
      });
    } catch (error) {
      const apiError: ApiError = error as ApiError;
      throw new Error(`Failed to delete reviews: ${apiError.message}`);
    }
  },

  async getTrashed(
    page: number = 1,
    keywords: string = "",
    rating: string = ""
  ): Promise<ApiResponse<ReviewData[]>> {
    try {
      return await apiRequest({
        method: "get",
        url: `/getTrashed/review`,
        params: { page, keywords, rating },
      });
    } catch (error) {
      const apiError: ApiError = error as ApiError;
      throw new Error(`Failed to get trashed reviews: ${apiError.message}`);
    }
  },

  async restore(reviewIds: number[] = []): Promise<ApiResponse<null>> {
    try {
      return await apiRequest({
        method: "put",
        url: `/restore/review`,
        data: { ids: reviewIds },
      });
    } catch (error) {
      const apiError: ApiError = error as ApiError;
      throw new Error(`Failed to restore reviews: ${apiError.message}`);
    }
  },
};

export default reviewService;
