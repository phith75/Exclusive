export interface Book {
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
  quantity_left: number;
  description: string;
  short_description: string;
  borrowed_count?: number;
  wishlists: any[];
  reviews: ReviewData[];
  avg_rating: number;
  rating_count: number;
  images: Array[];
}
export interface Title {
  title: string;
  subtitle: string;
}
export interface CreateReviewData {
  book_id: string;
  rating: number;
  description: string;
}

export interface ReviewData {
  id: number;
  book_id: number;
  user_id: number;
  user_name: string;
  book_name: string;
  rating: number;
  description: string;
  date: string;
}
