import api from "@/api";
import { BaseQuery, PaginatedResponse } from "../../types/api.types";
import { CustomerItem } from "../../types/Customer.types";

export async function listCustomers(params: BaseQuery) {
  const result = (await api.get("customers", params)) as unknown;

  return result as PaginatedResponse<CustomerItem>;
}
