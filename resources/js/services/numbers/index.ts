import api from "@/api";
import { ApiResponse } from "../../types/api.types";
import {
  AdminPaidNumbersRequest,
  AllCustomerNumbers,
  CustomerNumbersRequest,
  ReserveNumbersRequest,
  ReserveNumbersResponse,
} from "../../types/Number.types";

export async function listNumbers(contestId: number, customerId: number) {
  const result = (await api.get(
    `numbers/${contestId}/customer/${customerId}`
  )) as unknown;

  return result as AllCustomerNumbers;
}

export async function getCustomerNumbers(
  contestId: number,
  data: CustomerNumbersRequest
) {
  return api.post(`numbers/${contestId}/customer-numbers`, data);
}

export async function reserveNumbers(
  contestId: number,
  order: ReserveNumbersRequest
) {
  const result = (await api.post(
    `numbers/${contestId}/reserve`,
    order
  )) as unknown;

  return result as ReserveNumbersResponse;
}

export async function freeNumbers(contestId: number) {
  const result = (await api.post(`numbers/${contestId}/free`, {})) as unknown;

  return result as ApiResponse;
}

export async function adminPaidNumbers(
  contestId: number,
  order: AdminPaidNumbersRequest
) {
  const result = (await api.post(
    `numbers/manage/${contestId}/paid`,
    order
  )) as unknown;

  return result as ApiResponse;
}

export async function adminFreeNumbers(contestId: number) {
  const result = (await api.post(
    `numbers/manage/${contestId}/free`,
    {}
  )) as unknown;

  return result as ApiResponse;
}

export async function adminCancelOrder(contestId: number, orderId: number) {
  const result = (await api.remove(
    `numbers/manage/${contestId}/cancel-order/${orderId}`
  )) as unknown;

  return result as ApiResponse;
}
