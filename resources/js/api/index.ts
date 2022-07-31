import axios, { AxiosResponse } from "axios";

import { successInterceptor } from "./interceptors/success.interceptor";
import { errorInterceptor } from "./interceptors/error.interceptor";
import { tokenInterceptor } from "./interceptors/token.interceptor";

const instance = axios.create({
  baseURL: "/api/",
});

instance.interceptors.request.use(tokenInterceptor);
instance.interceptors.response.use(successInterceptor, errorInterceptor);

const get = async <T = any>(url: string, params: any = {}): Promise<T> => {
  const result = await instance.get<T>(url, {
    params: { ...params },
  });

  return result.data as T;
};

const post = async <T = any>(
  url: string,
  data: any = {},
  config: any = {}
): Promise<T> => {
  const result = await instance.post<T>(url, data, config);

  return result.data as T;
};

const update = async <T = any>(url: string, data?: any): Promise<T> => {
  const result = await instance.put<T>(url, data);

  return result.data as T;
};

const remove = async <T = any>(url: string): Promise<T> => {
  const result = await instance.delete<T>(url);

  return result.data as T;
};

const patch = async <T = any>(url: string, data?: any): Promise<T> => {
  const result = await instance.patch<T>(url, data);

  return result.data as T;
};

const upload = async <T = any>(url: string, data?: FormData): Promise<T> => {
  const headers = { "Content-Type": "multipart/form-data" };
  const result = await instance.post<T>(url, data, {
    headers,
  });

  return result.data as T;
};

const api = {
  get,
  post,
  update,
  remove,
  upload,
  patch,
};

export default api;
