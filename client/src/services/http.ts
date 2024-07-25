import axios, {
  type AxiosResponse,
  AxiosError,
  type InternalAxiosRequestConfig,
} from "axios";
import { useAuthStore } from "@/stores/authStore";

const http = axios.create({
  baseURL: import.meta.env.VITE_BASE_URL_API as string,
});

http.interceptors.request.use(
  (config: InternalAxiosRequestConfig) => {
    const authStore = useAuthStore();
    const token = authStore.getToken;

    if (token) {
      config.headers = {
        ...config.headers,
        Authorization: `Bearer ${token}`,
      };
    }

    return config;
  },
  (error: AxiosError) => {
    return Promise.reject(error);
  }
);
http.interceptors.response.use(
  (response: AxiosResponse) => {
    return response.data;
  },
  async (error: AxiosError) => {
    return Promise.reject(error);
  }
);

export default http;
