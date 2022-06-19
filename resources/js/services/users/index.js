import api from "@/api";

export async function listUsers(search) {
  return api.get("users", search);
}
