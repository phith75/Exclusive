export interface BookData {
  id: number;
  thumbnail: string;
  name: string;
  slug: string;
  category_id?: number;
  category_name?: string;
  author_id?: number;
  author_name?: string;
  publisher_id?: number;
  publisher_name?: string;
  quantity: number;
  description: string;
  short_description: string;
  borrowed_count?: number;
  wishlists: any[];
  reviews: any[];
  images?: any[];
}

// có thumbnail là 1 file ảnh, images lưu những file ảnh
export interface CreateBookData {
  name: string;
  thumbnail: file;
  category_id: number | string;
  author_id: number | string;
  publisher_id: number | string;
  quantity: number;
  description: string;
  short_description: string;
  images: (File | string)[] | undefined;
}

export interface UpdateBookData {
  name: string;
  thumbnail: File | string;
  category_id: number | string;
  author_id: number | string;
  publisher_id: number | string;;
  quantity: number;
  description: string;
  short_description: string;
  images: (File | string)[] | undefined;
  old_images: string[] | undefined;
}
