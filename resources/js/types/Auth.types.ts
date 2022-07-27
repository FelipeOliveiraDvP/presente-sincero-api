import { ApiResponse } from "./api.types";

export interface AuthUser {
  id: number;
  name: string;
  whatsapp: string;
  role?: string;
  email?: string | null;
  username?: string | null;
  avatar?: string | null;
  created_at?: string;
  updated_at?: string;
}

export interface AuthRequest {
  user: string;
}

export interface AuthLoginRequest extends AuthRequest {
  password?: string;
}

export type AuthRegisterRequest = {
  name: string;
  whatsapp: string;
  username: string;
  email: string;
  password: string;
  password_confirmation: string;
};

export type AuthResetRequest = {
  code: string;
  password: string;
  password_confirmation: string;
};

export type AuthProfileRequest = {
  whatsapp: string;
};

export interface AuthResponse {
  user: AuthUser;
  token: string;
}

export type AuthProfileResponse = ApiResponse & {
  user: AuthUser;
};

export interface AuthSimpleLoginRequest {
  whatsapp: string;
  name?: string;
  new_account?: boolean;
}

export interface AuthSimpleLoginResponse {
  message: string;
  new_account: boolean;
}
