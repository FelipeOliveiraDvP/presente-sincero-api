import { BaseQuery } from "./api.types";

export type BankAccountType = "BANK" | "PIX";

export interface BankAccountRequest {
  type: BankAccountType;
  name: string;
  main: boolean;
  cc?: string | null;
  agency?: string | null;
  dv?: string | null;
  key?: string | null;
}

export interface BankAccountResponse {
  message: string;
  bank_account: BankAccountItem;
}

export interface ListBankQuery extends BaseQuery {}

export interface BankAccountItem {
  id: number;
  user_id: number;
  name: string;
  cc?: string | null;
  agency?: string | null;
  dv?: string | null;
  key?: string | null;
  main: boolean;
  type: BankAccountType;
  created_at: string | null;
  updated_at: string | null;
  deleted_at: string | null;
}

export interface UpdateMercadoToken {
  mp_access_token: string;
}
