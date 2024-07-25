export interface ReviewData {
  id: number;
  book_id: number;
  user_id: number;
  user_name: string;
  book_name: string;
  description: string;
  rating: number;
  status: number;
}

export interface CreateReviewData {
  book_id: number;
  user_id: number;
  description: string;
  rating: number;
}

export interface UpdateStatusReviewData {
  status: number;
}
