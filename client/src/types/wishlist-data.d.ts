export interface WishlistData {
  id: number;
  user_id: number;
  book_id: number;
  book: {
    id: number;
    quantity: number;
    quantity_left: number;
    book_id: number;
    book_name: string;
    expected_refund_time: string;
  };
}
export interface CreateOrDeleteWishlist {
  book_id: number;
}
