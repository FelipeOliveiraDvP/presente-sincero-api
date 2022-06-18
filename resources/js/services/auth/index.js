import api from "@/api";

export async function login(credentials) {
  return api.post("auth/login", credentials);
}

export async function simpleLogin(credentials) {
  return api.post("auth/simple-login", credentials);
}

export async function register(credentials) {
  return api.post("auth/register", credentials);
}

export async function forgot(credentials) {
  return api.post("auth/forgot", credentials);
}

export async function verify(code) {
  return api.get(`auth/verify/${code}`);
}

export async function reset(credentials) {
  return api.update("auth/reset", credentials);
}

export async function getProfile() {
  return api.get("auth/profile");
}

export async function editProfile(credentials) {
  return api.update("auth/profile", credentials);
}
