import api from "@/api";
import { ApiResponse } from "@/types/api.types";
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
} from "@/types/Auth.types";

export async function login(credentials: AuthLoginRequest) {
  return api.post<AuthResponse>("auth/login", credentials);

  // const result = (await api.post("auth/login", credentials)) as unknown;

  // return result as AuthResponse;
}

export async function simpleLogin(credentials: AuthSimpleLoginRequest) {
  return api.post<AuthSimpleLoginResponse>("auth/simple-login", credentials);

  // const result = (await api.post("auth/simple-login", credentials)) as unknown;

  // return result as AuthResponse | AuthSimpleLoginResponse;
}

export async function register(credentials: AuthRegisterRequest) {
  return api.post<AuthResponse>("auth/register", credentials);

  // const result = (await api.post("auth/register", credentials)) as unknown;

  // return result as AuthResponse;
}

export async function forgot(credentials: AuthRequest) {
  return api.post<ApiResponse>("auth/forgot", credentials);

  // const result = (await api.post("auth/forgot", credentials)) as unknown;

  // return result as ApiResponse;
}

export async function verify(code: string) {
  return api.get<ApiResponse>(`auth/verify/${code}`);

  // const result = (await api.get(`auth/verify/${code}`)) as unknown;

  // return result as ApiResponse;
}

export async function reset(credentials: AuthResetRequest) {
  return api.update<AuthResponse>("auth/reset", credentials);

  // const result = (await api.update("auth/reset", credentials)) as unknown;

  // return result as AuthResponse;
}

export async function getProfile() {
  return api.get<AuthProfileResponse>("auth/profile");

  // const result = (await api.get<AuthProfileResponse>(
  //   "auth/profile"
  // )) as unknown;

  // return result as AuthProfileResponse;
}

export async function editProfile(credentials: AuthProfileRequest) {
  return api.update<AuthProfileResponse>("auth/profile", credentials);
  // const result = (await api.update("auth/profile", credentials)) as unknown;

  // return result as AuthProfileResponse;
}
