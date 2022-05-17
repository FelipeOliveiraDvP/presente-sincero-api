import axios from "axios";

import onSuccess from "./interceptors/success.interceptor";
import onError from "./interceptors/error.interceptor";

const instance = axios.create({
    baseURL: `http://localhost:8000/api/`,
});

instance.interceptors.response.use(onSuccess, onError);

const get = (url, params) => {
    return instance.get(url, { params: { ...params } });
};

const api = {
    get,
};

export default api;
