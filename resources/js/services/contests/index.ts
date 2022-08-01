import api from "@/api";
import {
  ApiResponse,
  BaseQuery,
  PaginatedResponse,
} from "../../types/api.types";
import {
  ContestDetail,
  ContestItem,
  ContestOrderItem,
  ContestRequest,
  ListContestsQuery,
} from "../../types/Contest.types";

export async function listContests(params: ListContestsQuery) {
  const result = (await api.get("contests", params)) as unknown;

  return result as PaginatedResponse<ContestItem>;
}

export async function listContestsBySeller(
  username: string,
  params: ListContestsQuery
) {
  const result = (await api.get(`contests/${username}`, params)) as unknown;

  return result as PaginatedResponse<ContestItem>;
}

export async function getContestBySlug(username: string, slug: string) {
  const result = (await api.get(`contests/${username}/${slug}`)) as unknown;

  return result as ContestDetail;
}

export async function listContestsByUser(params: ListContestsQuery) {
  const result = (await api.get("contests/manage", params)) as unknown;

  return result as PaginatedResponse<ContestItem>;
}

export async function getContest(id: number) {
  const result = (await api.get(`contests/manage/${id}`)) as unknown;

  return result as ContestDetail;
}

export async function listContestOrders(id: number, params: BaseQuery) {
  const result = (await api.get(
    `contests/manage/${id}/orders`,
    params
  )) as unknown;

  return result as PaginatedResponse<ContestOrderItem>;
}

export async function createContest(contest: ContestRequest) {
  const result = (await api.post("contests/manage", contest)) as unknown;

  return result as ApiResponse;
}

export async function editContest(id: number, contest: ContestRequest) {
  const result = (await api.update(
    `contests/manage/${id}`,
    contest
  )) as unknown;

  return result as ApiResponse;
}

export async function finishContest(id: number, number: string) {
  const result = (await api.update(
    `contests/manage/${id}/finish/${number}`,
    {}
  )) as unknown;

  return result as ApiResponse;
}
