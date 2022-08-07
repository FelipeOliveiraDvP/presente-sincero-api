import api from "@/api";
import { ApiResponse } from "@/types/api.types";
import {
  AllCustomerNumbers,
  CustomerNumbersRequest,
  NumberStatusResponse,
  ReserveNumbersRequest,
  ReserveNumbersResponse,
} from "@/types/Number.types";

export async function listNumbers(contestId: number, customerId: number) {
  return api.get<AllCustomerNumbers>(
    `numbers/${contestId}/customer/${customerId}`
  );
}

export async function getContestNumbersStatus(contestId?: number) {
  return api.get<NumberStatusResponse>(`numbers/${contestId}/status`);
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
  return api.post<ReserveNumbersResponse>(
    `numbers/${contestId}/reserve`,
    order
  );
}

export async function freeNumbers(contestId: number) {
  return api.remove<ApiResponse>(`numbers/${contestId}/free`);
}

export async function adminPaidOrder(contestId: number, orderId: number) {
  return api.update<ApiResponse>(
    `numbers/manage/${contestId}/orders/${orderId}/paid`,
    {}
  );
}

export async function adminCancelOrder(contestId: number, orderId: number) {
  return api.remove<ApiResponse>(
    `numbers/manage/${contestId}/orders/${orderId}/cancel`
  );
}
