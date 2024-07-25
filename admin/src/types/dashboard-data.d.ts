// ApiResponse interface
export interface ApiResponse<T> {
  data: T;
}

export interface DashboardData {
  total_users: number;
  total_books: number;
  total_lend_tickets: number;
  total_books_available: number;
}

export interface TopBooksData {
  name: string;
  lend_tickets_count: number;
  thumbnail: string;
}

export interface TopCustomersData {
  user_code: string;
  name: string;
  num_borrowed_books: number;
}

export interface MonthlyBorrowedData {
  month: string;
  borrow_count: number;
}

export interface BooksByCategoryData {
  name: string;
  book_count: number;
}

export interface TopFavoriteBooksData {
  name: string;
  wishlists_count: number;
  thumbnail: string;
}
