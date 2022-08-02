import api from "@/api";
import { ApiResponse, BaseQuery, PaginatedResponse } from "@/types/api.types";
import { SellerItem } from "@/types/Seller.types";

export async function listSellers(params: BaseQuery) {
  return api.get<PaginatedResponse<SellerItem>>("sellers", params);
}

export async function approveSeller(sellerId: number): Promise<ApiResponse> {
  return api.update<ApiResponse>(`sellers/${sellerId}/approve`, {});
}

export async function toggleSeller(sellerId: number): Promise<ApiResponse> {
  const result = (await api.patch(`sellers/${sellerId}/toggle`, {})) as unknown;

  return result as ApiResponse;
}
