import apiRequest from "@/services/apiService";
import type { ApiResponse } from "@/types/api-response";
import type { Book, CreateReviewData } from "@/types";

const bookService = {
  async getBooks(
    page: number = 1,
    keywords: string = "",
    author_id: string = "",
    publisher_id: string = "",
    category_id: string = ""
  ): Promise<ApiResponse<Book[]>> {
    return await apiRequest({
      method: "get",
      url: `/book`,
      params: { page, keywords, author_id, publisher_id, category_id },
    });
  },

  async getBookById(bookId: string): Promise<ApiResponse<Book>> {
    return await apiRequest({
      method: "get",
      url: `/book/${bookId}`,
    });
  },
  async getMostBorrowedBooks(): Promise<ApiResponse<Book[]>> {
    return await apiRequest({
      method: "get",
      url: `/most-borrowed/book`,
    });
  },

  async getRecommendedBooks(): Promise<ApiResponse<Book[]>> {
    return await apiRequest({
      method: "get",
      url: `/most-borrowed/book`,
    });
  },
  async submitReview(
    data: CreateReviewData
  ): Promise<ApiResponse<CreateReviewData>> {
    return await apiRequest({
      method: "post",
      url: `/review`,
      data,
    });
  },
  //review
  async getReviews(
    id: string | number,
    page: number = 1,
    keywords: string = ""
  ): Promise<ApiResponse<any[]>> {
    return await apiRequest({
      method: "get",
      url: `/review/${id}`,
      params: { page, keywords },
    });
  },
};

export default bookService;
