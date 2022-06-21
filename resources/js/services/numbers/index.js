import api from "@/api";

export async function listNumbers(contestId, params) {
  return api.get(`numbers/${contestId}`, params);
}

export async function getCustomerNumbers(contestId, data) {
  return api.post(`numbers/${contestId}/customer-numbers`, data);
}

export async function freeNumbers(contestId, numbers) {
  return api.post(`numbers/${contestId}/free`, numbers);
}

export async function reserveNumbers(contestId, order) {
  return api.post(`numbers/${contestId}/reserve`, order);
}

export async function adminFreeNumbers(contestId) {
  return api.post(`numbers/${contestId}/manage/free`, {});
}

export async function adminPaidNumbers(contestId, customer) {
  return api.post(`numbers/${contestId}/manage/paid`, customer);
}
