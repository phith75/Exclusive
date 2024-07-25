import apiRequest from "@/services/apiService";
import type { ApiResponse } from "@/types/api-response";
import type {
  TicketData,
  CreateTicketData,
  UpdateTicketData,
} from "@/types/index";

const ticketService = {
  async getTickets(
    page: number = 1,
    keywords: string = "",
    status: string = "",
    start_date: string = "",
    end_date: string = ""
  ): Promise<ApiResponse<TicketData[]>> {
    return await apiRequest({
      method: "get",
      url: `/ticket`,
      params: { page, keywords, status, start_date, end_date },
    });
  },

  async createTicket(
    data: CreateTicketData
  ): Promise<ApiResponse<CreateTicketData>> {
    const formData = new FormData();
    formData.append("address", data.address);
    formData.append("phone", data.phone);
    formData.append("user_id", data.user_id.toString());
    data.book_data.forEach((book, index) => {
      formData.append(`book_data[${index}][book_id]`, book.book_id.toString());
      formData.append(`book_data[${index}][book_name]`, book.book_name);
      formData.append(
        `book_data[${index}][quantity]`,
        book.quantity.toString()
      );
      formData.append(
        `book_data[${index}][expected_refund_time]`,
        book.expected_refund_time
      );
    });
    formData.append("note", data.note);

    return await apiRequest({
      method: "post",
      url: `/ticket`,
      data: formData,
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
};

export default ticketService;
