import api from "@/api";

export async function listBankAccounts(params) {
  return api.get("bank-accounts", params);
}

export async function createBankAccount(bankAccount) {
  return api.post("bank-accounts", bankAccount);
}

export async function editBankAccount(id, bankAccount) {
  return api.update(`bank-accounts/${id}`, bankAccount);
}

export async function removeBankAccount(id) {
  return api.remove(`bank-accounts/${id}`);
}
