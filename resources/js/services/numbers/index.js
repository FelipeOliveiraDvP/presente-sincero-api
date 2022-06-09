import api from "@/api";

export async function freeNumbers(contestId, numbers) {
  return api.post(`numbers/${contestId}/free`, numbers);
}

export async function reserveNumbers(contestId, order) {
  return api.post(`numbers/${contestId}/reserve`, order);
}
