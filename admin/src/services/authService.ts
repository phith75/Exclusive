import apiRequest from "@/services/apiService";

interface LoginResponse {
  data: {
    access_token: string;
    user: object;
  };
}

const authService = {
  async login(email: string, password: string): Promise<LoginResponse> {
    return apiRequest({
      method: "post",
      url: "/auth/login",
      data: { email, password },
    });
  },
  async refreshToken(): Promise<LoginResponse> {
    return apiRequest({
      method: "post",
      url: "/auth/refresh",
    });
  },
};

export default authService;
