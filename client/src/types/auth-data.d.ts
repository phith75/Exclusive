export interface LoginResponse {
  data: {
    access_token: string;
    user: User;
  };
}
export interface meResponse {
  data: User[];
}

export interface User {
  id: number;
  name: string;
  email: string;
  role: number;
  role_name: string;
  status: number;
  google_id?: string;
  user_code: string;
  num_borrowed_books: number;
  phone?: string;
  date_of_birth?: string;
  address?: string;
  gender: string;
  google_id?: string;
  avatar?: string;
  district_id?: number;
  province_id?: number;
  province_name?: string;
  district_name?: string;
  ward_name?: string;
  ward_id?: number;
}
export interface updateProfile {
  name?: string;
  phone?: string;
  gender?: number;
  address?: string;
  province_id?: number;
  district_id?: number;
  ward_id?: number;
  date_of_birth?: string;
}
