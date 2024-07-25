import axios, {
  type AxiosResponse,
  AxiosError,
  type InternalAxiosRequestConfig,
} from "axios";
import { useAuthStore } from "@/stores/authStore";
import { useLoadingStore } from "@/stores/loadingStore";

const http = axios.create({
  baseURL: import.meta.env.VITE_BASE_URL_API as string,
});

let isRefreshing = false;
let failedQueue: Array<{
  resolve: (token: string) => void;
  reject: (reason?: any) => void;
}> = [];

const processQueue = (
  error: AxiosError | null,
  token: string | null = null
) => {
  failedQueue.forEach((prom) => {
    if (error) {
      prom.reject(error);
    } else {
      prom.resolve(token as string);
    }
  });

  failedQueue = [];
};

http.interceptors.request.use(
  (config: InternalAxiosRequestConfig) => {
    const loadingStore = useLoadingStore();
    loadingStore.increaseLoadingCount();

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
    const loadingStore = useLoadingStore();
    loadingStore.decreaseLoadingCount();
    return Promise.reject(error);
  }
);

http.interceptors.response.use(
  (response: AxiosResponse) => {
    const loadingStore = useLoadingStore();
    loadingStore.decreaseLoadingCount();
    return response.data;
  },
  async (error: AxiosError) => {
    const loadingStore = useLoadingStore();
    loadingStore.decreaseLoadingCount();

    const authStore = useAuthStore();
    const originalRequest = error.config as InternalAxiosRequestConfig & {
      _retry?: boolean;
    };

    if (error.response?.status === 401 && !originalRequest._retry) {
      if (isRefreshing) {
        return new Promise((resolve, reject) => {
          failedQueue.push({ resolve, reject });
        })
          .then((token) => {
            originalRequest.headers = {
              ...originalRequest.headers,
              Authorization: `Bearer ${token}`,
            };
            return http(originalRequest);
          })
          .catch(Promise.reject);
      }

      originalRequest._retry = true;
      isRefreshing = true;

      return new Promise((resolve, reject) => {
        authStore
          .refreshAuthToken()
          .then((newToken: string) => {
            authStore.setToken(newToken);
            http.defaults.headers.common["Authorization"] =
              `Bearer ${newToken}`;
            originalRequest.headers = {
              ...originalRequest.headers,
              Authorization: `Bearer ${newToken}`,
            };
            processQueue(null, newToken);
            resolve(http(originalRequest));
          })
          .catch((err: AxiosError) => {
            processQueue(err, null);
            authStore.clearToken();
            reject(err);
          })
          .finally(() => {
            isRefreshing = false;
          });
      });
    }

    return Promise.reject(error);
  }
);

export default http;
