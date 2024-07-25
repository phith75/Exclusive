export interface TicketData {
  id: number;
  lend_ticket_code: string;
  user_id: number;
  name: null;
  address: string;
  email: string;
  status: string | number;
  borrowed_date: string;
  phone: string;
  note: string;
  ticket_detail: TicketDetail[];
}

export interface TicketDetail {
  id: number;
  lend_ticket_id: number;
  book_id: number;
  book_name: string;
  quantity: number;
  status: string | number;
  expected_refund_time: string;
  returned_date: null;
}

export interface CreateTicketData {
  user_id: number;
  address: string;
  phone: string;
  book_data: BookData[];
  note: string;
}
export interface BookData {
  book_id: number;
  quantity: number;
  book_name: string;
  expected_refund_time: string;
}
export interface UpdateTicketData {
  status: string | number;
}
