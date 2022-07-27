import { AxiosResponse } from "axios";

export const successInterceptor = (response: AxiosResponse) => response.data;
