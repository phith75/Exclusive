export interface ApiResponse<T> {
  data: T;
  success: 1 | 0;
  pagination?: Pagination;
  message?: string;
  errors?: string;
}
export interface Pagination {
  current_page: number;
  last_page: number;
  per_page: number;
}

export interface ApiError {
  message: string;
  status?: number;
  [key: string]: any;
}

export interface FormErrors {
  [key: string]: string[] | null;
}
