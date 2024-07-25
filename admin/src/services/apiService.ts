import http from "@/services/http";
import type { AxiosRequestConfig, Method } from "axios";

interface ApiRequestOptions {
  method: Method;
  url: string;
  token?: string | null;
  data?: FormData | any;
  params?: any;
}

const apiRequest = async ({
  method,
  url,
  data = null,
  params = null,
}: ApiRequestOptions): Promise<any> => {
  const headers: Record<string, string> = {
    accept: "application/json",
  };

  if (data instanceof FormData) {
    headers["Content-Type"] = "multipart/form-data";
  } else {
    headers["Content-Type"] = "application/json";
  }

  const config: AxiosRequestConfig = {
    method,
    url,
    headers,
    ...(data ? { data } : {}),
    ...(params ? { params } : {}),
  };

  const response = await http(config);
  return response;
};

export default apiRequest;
