import axios from "axios";

import { successInterceptor } from "./interceptors/success.interceptor";
import { errorInterceptor } from "./interceptors/error.interceptor";
import { tokenInterceptor } from "./interceptors/token.interceptor";

const instance = axios.create({
  baseURL: "/api/",
});

instance.interceptors.request.use(tokenInterceptor);
instance.interceptors.response.use(successInterceptor, errorInterceptor);

const get = (url: string, params: any = {}) => {
  return instance.get(url, { params: { ...params } });
};

const post = (url: string, data: any = {}, config: any = {}) => {
  return instance.post(url, data, config);
};

const update = (url: string, data?: any) => {
  return instance.put(url, data);
};

const remove = (url: string) => {
  return instance.delete(url);
};

const patch = (url: string, data?: any) => {
  return instance.patch(url, data);
};

const upload = (url: string, data?: FormData) => {
  return instance.post(url, data, {
    headers: { "Content-Type": "multipart/form-data" },
  });
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
