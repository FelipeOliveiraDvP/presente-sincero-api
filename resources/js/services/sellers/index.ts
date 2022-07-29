import api from "@/api";
import { ApiResponse, BaseQuery, PaginatedResponse } from "@/types/api.types";
import { SellerItem } from "@/types/Seller.types";

export async function listSellers(params: BaseQuery) {
  const result = (await api.get("sellers", params)) as unknown;

  return result as PaginatedResponse<SellerItem>;
}

export async function approveSeller(sellerId: number) {
  const result = (await api.update(
    `sellers/${sellerId}/approve`,
    {}
  )) as unknown;

  return result as ApiResponse;
}

export async function toggleSeller(sellerId: number) {
  const result = (await api.patch(`sellers/${sellerId}/toggle`, {})) as unknown;

  return result as ApiResponse;
}
