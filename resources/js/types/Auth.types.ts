import { ApiResponse } from "./api.types";

export interface AuthUser {
  id: number;
  name: string;
  whatsapp: string;
  role?: string;
  email?: string | null;
  username?: string | null;
  avatar?: string | null;
  mp_access_token?: string | null;
  seller_approved?: boolean;
  blocked?: boolean;
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
  name?: string;
  whatsapp?: string;
  email?: string | null;
  username?: string | null;
  password?: string;
  password_confirmation?: string;
};

export interface AuthResponse {
  user: AuthUser;
  token: string;
}

export interface AuthProfileResponse extends ApiResponse {
  user: AuthUser;
}

export interface AuthSimpleLoginRequest {
  whatsapp: string;
  name?: string;
  new_account?: boolean;
}

export interface AuthSimpleLoginResponse extends AuthResponse {
  message: string;
  new_account: boolean;
}
