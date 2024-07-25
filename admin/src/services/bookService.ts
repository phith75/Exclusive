import apiRequest from "@/services/apiService";
import type { ApiResponse, ApiError } from "@/types/api-response";
import type {
  AuthorData,
  BookData,
  CategoryData,
  CreateBookData,
  PublisherData,
  UpdateBookData,
} from "@/types/index";

const bookService = {
  async getBooks(
    page: number = 1,
    keywords: string = "",
    category_id: string = "",
    author_id: string = "",
    publisher_id: string = ""
  ): Promise<ApiResponse<BookData[]>> {
    return await apiRequest({
      method: "get",
      url: `/book`,
      params: { page, keywords, author_id, publisher_id, category_id },
    });
  },
  // get all category
  async getCategories(): Promise<ApiResponse<CategoryData[]>> {
    return await apiRequest({
      method: "get",
      url: `getAll/category`,
    });
  },
  // get all author
  async getAuthors(): Promise<ApiResponse<AuthorData[]>> {
    return await apiRequest({
      method: "get",
      url: `getAll/author`,
    });
  },
  // get all publisher
  async getPublishers(): Promise<ApiResponse<PublisherData[]>> {
    return await apiRequest({
      method: "get",
      url: `getAll/publisher`,
    });
  },
  async createBook(data: CreateBookData): Promise<ApiResponse<CreateBookData>> {
    const formData = new FormData();
    formData.append("name", data.name);
    if (data.category_id) {
      formData.append("category_id", data.category_id.toString());
    }
    if (data.author_id) {
      formData.append("author_id", data.author_id.toString());
    }
    if (data.publisher_id) {
      formData.append("publisher_id", data.publisher_id.toString());
    }
    formData.append("quantity", data.quantity.toString());
    formData.append("description", data.description);
    formData.append("short_description", data.short_description);
    if (data.thumbnail) {
      formData.append("thumbnail", data.thumbnail);
    }
    if (data.images) {
      data.images.forEach((image, index) => {
        formData.append(`images[${index}]`, image);
      });
    }

    return await apiRequest({
      method: "post",
      url: `/book`,
      data: formData,
    });
  },

  async getBookById(bookId: string): Promise<ApiResponse<BookData>> {
    return await apiRequest({
      method: "get",
      url: `/book/${bookId}`,
    });
  },

  async updateBook(
    bookId: string,
    data: UpdateBookData
  ): Promise<ApiResponse<UpdateBookData>> {
    const formData = new FormData();
    formData.append("name", data.name);
    if (data.category_id) {
      formData.append("category_id", data.category_id.toString());
    }
    if (data.author_id) {
      formData.append("author_id", data.author_id.toString());
    }
    if (data.publisher_id) {
      formData.append("publisher_id", data.publisher_id.toString());
    }
    formData.append("quantity", data.quantity.toString());
    formData.append("description", data.description);
    formData.append("short_description", data.short_description);
    if (data.thumbnail) {
      formData.append("thumbnail", data.thumbnail);
    }
    if (data.images) {
      data.images.forEach((image, index) => {
        formData.append(`images[${index}]`, image);
      });
    }
    if (data.old_images) {
      data.old_images.forEach((image, index) => {
        formData.append(`old_images[${index}]`, image);
      });
    }

    return await apiRequest({
      method: "post",
      url: `/book/${bookId}?_method=PUT`,
      data: formData,
    });
  },

  async deleteBook(bookId: number): Promise<ApiResponse<null>> {
    return await apiRequest({
      method: "delete",
      url: `/book/${bookId}`,
    });
  },
  //deletemany

  async deleteManyBooks(bookIds: number[]): Promise<ApiResponse<null>> {
    return await apiRequest({
      method: "delete",
      url: `/deleteMany/book`,
      data: { ids: bookIds },
    });
  },
  async getTrashed(
    page: number = 1,
    keywords: string = "",
    category_id: string = "",
    author_id: string = "",
    publisher_id: string = ""
  ): Promise<ApiResponse<BookData[]>> {
    try {
      return await apiRequest({
        method: "get",
        url: `/getTrashed/book`,
        params: { page, keywords, author_id, publisher_id, category_id },
      });
    } catch (error) {
      const apiError: ApiError = error as ApiError;
      throw new Error(`Failed to get trashed books: ${apiError.message}`);
    }
  },

  async restore(bookIds: number[] = []): Promise<ApiResponse<null>> {
    try {
      return await apiRequest({
        method: "put",
        url: `/restore/book`,
        data: { ids: bookIds },
      });
    } catch (error) {
      const apiError: ApiError = error as ApiError;
      throw new Error(`Failed to restore books: ${apiError.message}`);
    }
  },
};

export default bookService;
