import api from "@/api";
import { ApiResponse, PaginatedResponse } from "@/types/api.types";
import {
  BankAccountItem,
  BankAccountRequest,
  BankAccountResponse,
  ListBankQuery,
  UpdateMercadoToken,
} from "@/types/BankAccount.types";

export async function listBankAccounts(params: ListBankQuery) {
  const result = (await api.get("bank-accounts", params)) as unknown;

  return result as PaginatedResponse<BankAccountItem>;
}

export async function createBankAccount(bankAccount: BankAccountRequest) {
  const result = (await api.post("bank-accounts", bankAccount)) as unknown;

  return result as BankAccountResponse;
}

export async function editBankAccount(
  id: number,
  bankAccount: BankAccountRequest
) {
  const result = (await api.update(
    `bank-accounts/${id}`,
    bankAccount
  )) as unknown;

  return result as BankAccountResponse;
}

export async function removeBankAccount(id) {
  const result = (await api.remove(`bank-accounts/${id}`)) as unknown;

  return result as ApiResponse;
}

export async function saveMPAccessToken(data: UpdateMercadoToken) {
  const result = (await api.update(
    `bank-accounts/mercado-pago`,
    data
  )) as unknown;

  return result as ApiResponse;
}
