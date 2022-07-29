import { AxiosError } from "axios";

import router from "@/routes";
import { ErrorResponse } from "@/types/api.types";
import { useAuthStore } from "@/store/auth";

export const errorInterceptor = (error: AxiosError): Promise<ErrorResponse> => {
  const { authenticated, logout } = useAuthStore();

  if (error.response?.status === 401) {
    logout();
  }

  if (error.response?.status === 403) {
    authenticated ? router.push("/admin/403") : router.push("/403");
  }

  if (error.response?.status === 404) {
    authenticated ? router.push("/admin/404") : router.push("/404");
  }

  return Promise.reject(error.response?.data as ErrorResponse);
};
