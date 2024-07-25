import apiRequest from "@/services/apiService";
import type { ApiResponse, ApiError } from "@/types/api-response";
import type {
  TicketData,
  UpdateTicketData,
  BookDataName,
  UserDataName,
} from "@/types/index";

const ticketService = {
  async getTickets(
    page: number = 1,
    keywords: string = "",
    status: string = "",
    ticketDetail = "",
    startDate = "",
    endDate = ""
  ): Promise<ApiResponse<TicketData[]>> {
    return await apiRequest({
      method: "get",
      url: `/ticket`,
      params: {
        page,
        keywords,
        status,
        ticketDetail,
        start_date: startDate,
        end_date: endDate,
      },
    });
  },

  async getTicketById(
    ticketId: string | number
  ): Promise<ApiResponse<TicketData>> {
    return await apiRequest({
      method: "get",
      url: `/ticket/${ticketId}`,
    });
  },

  async changeStatusTicket(
    ticketId: string,
    data: UpdateTicketData
  ): Promise<ApiResponse<UpdateTicketData>> {
    return await apiRequest({
      method: "put",
      url: `/update-lend-ticket-status/${ticketId}`,
      data,
    });
  },

  async changeStatusDetails(
    ticketDetailId: number,
    data: UpdateTicketData
  ): Promise<ApiResponse<UpdateTicketData>> {
    return await apiRequest({
      method: "put",
      url: `/update-ticket-detail-status/${ticketDetailId}`,
      data,
    });
  },

  async getBooks(): Promise<ApiResponse<BookDataName[]>> {
    try {
      return await apiRequest({
        method: "get",
        url: `/book`,
      });
    } catch (error) {
      const apiError: ApiError = error as ApiError;
      throw new Error(`Failed to get books: ${apiError.message}`);
    }
  },

  async getUsers(): Promise<ApiResponse<UserDataName[]>> {
    try {
      return await apiRequest({
        method: "get",
        url: `/user`,
      });
    } catch (error) {
      const apiError: ApiError = error as ApiError;
      throw new Error(`Failed to get users: ${apiError.message}`);
    }
  },
};

export default ticketService;
