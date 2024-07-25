import apiRequest from "@/services/apiService";
import type { ApiResponse } from "@/types/api-response";
import type { Book, CreateOrDeleteWishlist, WishlistData } from "@/types";

const wishlistService = {
  async getWishlists(): Promise<ApiResponse<WishlistData[]>> {
    return await apiRequest({
      method: "get",
      url: `/wishlist`,
    });
  },
  async getWishlistsByUser(): Promise<ApiResponse<Book[]>> {
    return await apiRequest({
      method: "get",
      url: `/wishlist-user`,
    });
  },

  async createOrDeleteWishlist(
    bookId: number
  ): Promise<ApiResponse<CreateOrDeleteWishlist>> {
    return await apiRequest({
      method: "post",
      url: `/wishlist/`,
      data: {
        book_id: bookId,
      },
    });
  },
  //suggest for user
  async suggestForUser(): Promise<ApiResponse<Book[]>> {
    return await apiRequest({
      method: "get",
      url: `/suggestion`,
    });
  },
};
export default wishlistService;
