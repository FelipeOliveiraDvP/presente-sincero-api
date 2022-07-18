import axios from "axios";

import onSuccess from "./interceptors/success.interceptor";
import onError from "./interceptors/error.interceptor";
import tokenInterceptor from "./interceptors/token.interceptor";

const instance = axios.create({
  baseURL: "/api/",
});

instance.interceptors.request.use(tokenInterceptor);
instance.interceptors.response.use(onSuccess, onError);

const get = (url, params) => {
  return instance.get(url, { params: { ...params } });
};

const post = (url, data) => {
  return instance.post(url, data);
};

const update = (url, data) => {
  return instance.put(url, data);
};

const remove = (url) => {
  return instance.delete(url);
};

const upload = (url, data) => {
  return instance.post(url, data, {
    headers: { "Content-Type": "multipart/form-data" },
  });
};

const patch = (url, data) => {
  return instance.patch(url, data);
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
