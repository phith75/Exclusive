import apiRequest from "@/services/apiService";
import type {
  ApiResponse,
  DashboardData,
  TopBooksData,
  TopCustomersData,
  MonthlyBorrowedData,
  BooksByCategoryData,
  TopFavoriteBooksData,
} from "@/types";

const dashboardService = {
  // Get Status
  async getStatus(): Promise<ApiResponse<DashboardData>> {
    return apiRequest({
      method: "get",
      url: "/dashboard/status",
    });
  },

  // Get Top Customers Borrowing Books
  async getTopCustomersBorrowingBooks(
    interval: string,
    startDate?: string,
    endDate?: string
  ): Promise<ApiResponse<TopCustomersData[]>> {
    return apiRequest({
      method: "get",
      url: `/dashboard/top-customers-borrowing-books/${interval}`,
      params: { start_date: startDate, end_date: endDate },
    });
  },

  // Get Top Most Borrowed Books
  async getTopMostBorrowedBooks(
    interval: string,
    startDate?: string,
    endDate?: string
  ): Promise<ApiResponse<TopBooksData[]>> {
    return apiRequest({
      method: "get",
      url: `/dashboard/top-book-borrowed-the-most/${interval}`,
      params: { start_date: startDate, end_date: endDate },
    });
  },

  // Get Monthly Borrowed Books
  async getMonthlyBorrowedBooks(
    startDate?: string,
    endDate?: string
  ): Promise<ApiResponse<MonthlyBorrowedData[]>> {
    return apiRequest({
      method: "get",
      url: "/dashboard/monthly-borrowed-books",
      params: { start_date: startDate, end_date: endDate },
    });
  },

  // Get Books By Category
  async getBooksByCategory(
    interval: string,
    startDate?: string,
    endDate?: string
  ): Promise<ApiResponse<BooksByCategoryData[]>> {
    return apiRequest({
      method: "get",
      url: `/dashboard/books-by-category/${interval}`,
      params: { start_date: startDate, end_date: endDate },
    });
  },

  // Get Top 30 Favorite Books
  async getTop30FavoriteBooks(
    interval: string,
    startDate?: string,
    endDate?: string
  ): Promise<ApiResponse<TopFavoriteBooksData[]>> {
    return apiRequest({
      method: "get",
      url: `/dashboard/top-30-favorite-books/${interval}`,
      params: { start_date: startDate, end_date: endDate },
    });
  },
};

export default dashboardService;
