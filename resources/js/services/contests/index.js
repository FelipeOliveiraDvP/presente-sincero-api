import api from "@/api";

export async function listContests(params) {
  return api.get("contests", params);
}

export async function listContestsBySeller(username, params) {
  return api.get(`contests/${username}`, params);
}

export async function getContestBySlug(username, slug) {
  return api.get(`contests/${username}/${slug}`);
}

export async function listContestsByUser(params) {
  return api.get("contests/manage", params);
}

export async function getContest(id) {
  return api.get(`contests/manage/${id}`);
}

export async function listContestOrders(id, params) {
  return api.get(`contests/manage/${id}/orders`, params);
}

export async function createContest(contest) {
  return api.post("contests/manage", contest);
}

export async function editContest(id, contest) {
  return api.update(`contests/manage/${id}`, contest);
}

export async function finishContest(id, number) {
  return api.update(`contests/manage/${id}/finish/${number}`, {});
}
