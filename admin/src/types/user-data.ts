export interface UserData {
  id: number;
  user_code: string;
  name: string;
  email: string;
  status: number;
  role: number;
  role_name: string;
  address: string;
  phone: string;
  date_of_birth: string;
  gender: number;
  num_borrowed_books: number;
  province_id: number;
  province_name: string;
  district_id: number;
  district_name: string;
  ward_id: number;
  ward_name: string;
  reviews: any[];
}
export interface ImportStaffData {
  user_file: File | null;
}

export interface updateStatusUser {
  status: string;
}
export interface updateRoleUser {
  role: string;
}
export interface StaffList {
  id: number;
  name: string;
  email: string;
  user_code: string;
}
