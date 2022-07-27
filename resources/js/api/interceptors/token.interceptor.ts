import { AxiosRequestConfig } from "axios";
import { useAuthStore } from "@/store/auth";

export const tokenInterceptor = (
  config: AxiosRequestConfig
): AxiosRequestConfig => {
  const { token } = useAuthStore();

  return {
    ...config,
    headers: {
      Authorization: `Bearer ${token}`,
    },
  };
};
