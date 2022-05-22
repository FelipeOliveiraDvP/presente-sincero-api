import axios from "axios";

import onSuccess from "./interceptors/success.interceptor";
import onError from "./interceptors/error.interceptor";

const instance = axios.create({
    baseURL: `/api/`,
});

instance.interceptors.response.use(onSuccess, onError);

const get = (url, params) => {
    return instance.get(url, { params: { ...params } });
};

const post = (url, data) => {
    return instance.post(url, data);
};

const upload = (url, data) => {
    return instance.post(url, data, {
        headers: { "Content-Type": "multipart/form-data" },
    });
};

const api = {
    get,
    post,
    upload,
};

export default api;
