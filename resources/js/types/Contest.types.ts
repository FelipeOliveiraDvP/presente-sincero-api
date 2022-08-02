import { Dayjs } from "dayjs";

export interface ListContestsQuery {
  title?: string;
  limit?: number;
  page?: number;
}

export interface ContestItem {
  id: number;
  title: string;
  user_id: number;
  slug: string;
  short_description: string;
  start_date: string;
  price: number;
  quantity: number;
  paid_percentage: number;
  seller: {
    id: number;
    name: string;
    username: string;
  };
  gallery: Array<ContestImage>;
}

export interface ContestRequest {
  title: string;
  start_date: string | Dayjs;
  max_reserve_days: number;
  price: number;
  quantity: number;
  short_description: string;
  whatsapp_number: string;
  whatsapp_group: string;
  sales: Array<ContestSale>;
  bank_accounts: Array<number>;
  gallery: Array<string>;
}

export interface ContestEditRequest {
  contest_date?: string | Dayjs;
  max_reserve_days?: number;
  short_description?: string;
  whatsapp_number?: string;
  whatsapp_group?: string;
  sales?: Array<ContestSale>;
  bank_accounts?: Array<number>;
  gallery?: Array<string>;
}

export interface ContestDetail {
  id: number;
  user_id: number;
  winner_id: number | null;
  title: string;
  slug: string;
  start_date: string;
  max_reserve_days: number;
  show_percentage: boolean;
  use_custom_percentage: boolean;
  paid_percentage: number;
  custom_percentage: number;
  contest_date: string | null;
  price: number;
  quantity: number;
  short_description: string;
  whatsapp_number: string;
  whatsapp_group: string;
  numbers?: string;
  status: ContestStatusType;
  created_at: string | null;
  updated_at: string | null;
  deleted_at: string | null;
  gallery: Array<ContestImage>;
  bank_accounts: Array<ContestBankAccount>;
  sales: Array<ContestSale>;
}

export interface ContestBankAccount {
  id: number;
  name?: string;
  main: boolean;
}

export interface ContestImage {
  id?: number;
  contest_id?: number;
  path: string;
}

export interface ContestSale {
  id?: number;
  contest_id?: number;
  quantity: number;
  price: number;
}

export type ContestStatusType = "ACTIVE" | "FINISHED";

export interface ContestOrderItem {
  id: number;
  contest_id: number;
  user_id: number;
  total: number;
  numbers: string;
  status: ContestOrderStatus;
  user: {
    id: number;
    name: string;
    whatsapp: string;
  };
  contest: {
    id: number;
    title: string;
    price: number;
  };
  confirmed_at: string | null;
  created_at: string | null;
  updated_at: string | null;
}

export type ContestOrderStatus = "PENDING" | "CANCELED" | "CONFIRMED";
