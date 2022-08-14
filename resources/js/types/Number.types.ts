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

export type ContestCustomerNumbers = Array<{ id: number; numbers: string }>;

export interface ReserveNumbersRequest {
  random?: number;
  numbers?: Array<string>;
}

export interface ReserveNumbersResponse {
  message: string;
  order_id: number;
  processing: boolean;
}

export interface PaymentManual {
  user_id: number;
  order_id: number;
  mercado_pago: boolean;
}

export interface PaymentInformation {
  user_id: number;
  order_id: number;
  mercado_pago: boolean;
  payment: {
    payment_id: string;
    order_id: number;
    qrcode_base64: string;
    ticket_url: string;
    qr_code: string;
  };
}

export interface PaymentConfirmed {
  user_id: number;
  order_id: number;
  confirmed: boolean;
}
