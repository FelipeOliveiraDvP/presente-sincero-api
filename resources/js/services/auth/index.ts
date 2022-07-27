import api from "@/api";
import { ApiResponse } from "../../types/api.types";
import {
  AuthLoginRequest,
  AuthProfileRequest,
  AuthProfileResponse,
  AuthRegisterRequest,
  AuthRequest,
  AuthResetRequest,
  AuthResponse,
  AuthSimpleLoginRequest,
  AuthSimpleLoginResponse,
} from "../../types/Auth.types";

export async function login(credentials: AuthLoginRequest) {
  const result = (await api.post("auth/login", credentials)) as unknown;

  return result as AuthResponse;
}

export async function simpleLogin(credentials: AuthSimpleLoginRequest) {
  const result = (await api.post("auth/simple-login", credentials)) as unknown;

  return result as AuthResponse | AuthSimpleLoginResponse;
}

export async function register(credentials: AuthRegisterRequest) {
  const result = (await api.post("auth/register", credentials)) as unknown;

  return result as AuthResponse;
}

export async function forgot(credentials: AuthRequest) {
  const result = (await api.post("auth/forgot", credentials)) as unknown;

  return result as ApiResponse;
}

export async function verify(code: string) {
  const result = (await api.get(`auth/verify/${code}`)) as unknown;

  return result as ApiResponse;
}

export async function reset(credentials: AuthResetRequest) {
  const result = (await api.update("auth/reset", credentials)) as unknown;

  return result as AuthResponse;
}

export async function getProfile() {
  const result = (await api.get("auth/profile")) as unknown;

  return result as AuthProfileResponse;
}

export async function editProfile(credentials: AuthProfileRequest) {
  const result = (await api.update("auth/profile", credentials)) as unknown;

  return result as AuthProfileResponse;
}
