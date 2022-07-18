import api from "@/api";

export async function listCustomers(params) {
  return api.get("customers", params);
}
