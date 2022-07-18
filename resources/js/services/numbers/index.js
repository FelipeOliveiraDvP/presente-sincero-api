import api from "@/api";

export async function listNumbers(contestId, customerId) {
  return api.get(`numbers/${contestId}/customer/${customerId}`);
}

export async function getCustomerNumbers(contestId, data) {
  return api.post(`numbers/${contestId}/customer-numbers`, data);
}

export async function reserveNumbers(contestId, numbers) {
  return api.post(`numbers/${contestId}/reserve`, numbers);
}

export async function freeNumbers(contestId, numbers) {
  return api.post(`numbers/${contestId}/free`, numbers);
}

export async function adminPaidNumbers(contestId, order) {
  return api.post(`numbers/manage/${contestId}/paid`, order);
}

export async function adminFreeNumbers(contestId) {
  return api.post(`numbers/manage/${contestId}/free`, {});
}

export async function adminCancelOrder(contestId, orderId) {
  return api.remove(`numbers/manage/${contestId}/cancel-order/${orderId}`, {});
}
