export type NumberStatusType = "FREE" | "RESERVED" | "PAID";

export type AllCustomerNumbers = Array<NumberItem>;

export interface NumberItem {
  number: string;
  status: NumberStatusType;
  order_id: number | null;
  reserved_at: string | null;
  payment_at: string | null;
  customer: {
    id: number | null;
    name: string | null;
    whatsapp: string | null;
  };
}

export interface NumberStatusResponse {
  total: number;
  free: number;
  reserved: number;
  paid: number;
}

export interface CustomerNumbersRequest {
  whatsapp: string;
}

export type ContestCustomerNumbers = Array<{ id: number; numners: string }>;

export interface ReserveNumbersRequest {
  random?: number;
  numbers?: Array<string>;
}

export interface ReserveNumbersResponse {
  message: string;
  mercado_pago: boolean;
  payment?: {
    payment_id: number;
    order_id: number;
    qrcode_base64: string;
    qr_code: string;
    ticket_url: string;
  };
}

export interface AdminPaidNumbersRequest {
  customer_id: number;
  numbers: Array<string>;
}
