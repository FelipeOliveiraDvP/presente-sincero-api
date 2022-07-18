import api from "@/api";

export async function listSellers(params) {
  return api.get("sellers", params);
}

export async function approveSeller(sellerId) {
  return api.update(`sellers/${sellerId}/approve`, {});
}

export async function toggleSeller(sellerId) {
  return api.patch(`sellers/${sellerId}/toggle`, {});
}
