import { defineStore } from "pinia";
import { computed, ref, type Ref } from "vue";
import authService from "@/services/authService";
import { toast } from "vue3-toastify";
import router from "@/router";

interface LoginForm {
  email: string;
  password: string;
}

export const useAuthStore = defineStore("auth", () => {
  const token: Ref<string> = ref(localStorage.getItem("token") || "");
  const user: Ref<string | null> = ref(localStorage.getItem("user") || "");

  const setToken = (newToken: string): void => {
    token.value = newToken;
    localStorage.setItem("token", newToken);
  };

  const setRefreshToken = (newRefreshToken: string): void => {
    token.value = newRefreshToken;
    localStorage.setItem("token", newRefreshToken);
  };

  const setUser = (newUser: string): void => {
    user.value = newUser;
    localStorage.setItem("user", newUser);
  };

  const getToken = computed<string>(() => {
    return token.value || localStorage.getItem("token") || "";
  });

  const getUser = computed<string>(() => {
    return user.value || localStorage.getItem("user") || "";
  });

  const clearToken = (): void => {
    token.value = "";
    user.value = "";
    localStorage.removeItem("token");
    localStorage.removeItem("user");
  };

  const handleLogin = async (loginForm: LoginForm): Promise<void> => {
    try {
      const response = await authService.login(
        loginForm.email,
        loginForm.password
      );

      setToken(response.data.access_token);
      const userEncode = JSON.stringify(response.data.user);
      setUser(userEncode);
      router.push("admin/dashboard");
      setTimeout(() => {
        toast.success("Login successful");
      }, 200);
    } catch (error) {
      toast.error("Login failed");
    }
  };

  const refreshAuthToken = async (): Promise<string> => {
    try {
      const response = await authService.refreshToken();
      setToken(response.data.access_token);
      return response.data.access_token;
    } catch (error) {
      clearToken();
      router.push("/login");
      throw error;
    }

  };

  const logout = (): void => {
    clearToken();
    router.push("/login");
    setTimeout(() => {
      toast.success("Logout successful");
    }, 200);
  };

  return {
    token,
    user,
    handleLogin,
    setToken,
    setRefreshToken,
    setUser,
    getUser,
    clearToken,
    getToken,
    refreshAuthToken,
    logout,
  };
});
