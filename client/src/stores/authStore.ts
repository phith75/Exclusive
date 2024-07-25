import { defineStore } from "pinia";
import { computed, ref, type Ref } from "vue";
import authService from "@/services/authService";
import wishlistService from "@/services/wishlistService";
import { toast } from "vue3-toastify";
import router from "@/router";
import type { meResponse, User, WishlistData } from "@/types";

interface LoginForm {
  email: string;
  password: string;
}

export const useAuthStore = defineStore("auth", () => {
  const token: Ref<string> = ref(localStorage.getItem("token") || "");
  const user: Ref<User | null> = ref(null);
  const wishlistUser: Ref<WishlistData[]> = ref([]);

  const setToken = (newToken: string): void => {
    token.value = newToken;
    localStorage.setItem("token", newToken);
  };

  const setUser = (newUser: User): void => {
    user.value = newUser;
    localStorage.setItem("user", JSON.stringify(newUser));
  };

  const setWishlist = (newWishlist: WishlistData[]): void => {
    wishlistUser.value = newWishlist;
  };

  const getToken = computed<string>(() => {
    return token.value || localStorage.getItem("token") || "";
  });

  const getUser = computed<User | null>(() => {
    if (user.value) return user.value;
    const userString = localStorage.getItem("user");
    if (userString) {
      try {
        return JSON.parse(userString) as User;
      } catch {
        console.error("Error parsing user JSON from localStorage");
        return null;
      }
    }
    return null;
  });

  const clearToken = (): void => {
    token.value = "";
    user.value = null;
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
      setUser(response.data.user);
      const redirectPath = localStorage.getItem("redirectPath");
      if (redirectPath) {
        localStorage.removeItem("redirectPath");
        router.push(redirectPath);
      } else {
        router.push({ name: "Homepage" });
      }
      setTimeout(() => {
        toast.success("Login success");
      }, 500);
    } catch (errors: any) {
      throw errors;
    }
  };

  const handleRegister = async (loginForm: LoginForm): Promise<void> => {
    try {
      const response = await authService.register(
        loginForm.email,
        loginForm.password,
        loginForm.confirmPassword
      );
      setToken(response.data.access_token);
      setUser(response.data.user);
      router.push("/home");
      setTimeout(() => {
        toast.success("Register success");
      }, 500);
    } catch (errors: any) {
      throw errors;
    }
  };

  const me = async (
    access_token: string = getToken.value
  ): Promise<meResponse> => {
    try {
      const response = await authService.me(access_token);
      setUser(response.data);
      return response.data;
    } catch (errors: any) {
      toast.error("Login failed: " + (errors.response?.data?.errors || ""));
      throw errors;
    }
  };

  const refreshAuthToken = async () => {
    try {
      const response = await authService.refreshToken();
      setToken(response.data.access_token);
      return response.data.access_token;
    } catch (error) {
      clearToken();
      router.push("/login");
      throw error; // Ensure error is thrown
    }
  };

  const logout = () => {
    clearToken();
    wishlistUser.value = [];
    router.push("/login");
    setTimeout(() => {
      toast.success("Logout successful");
    }, 200);
    return Promise.resolve();
  };

  const isLogin = computed(() => {
    return !!getToken.value;
  });

  const getWishlist = computed<WishlistData[]>(() => {
    return wishlistUser.value;
  });

  const addToWishlist = async (bookId: number) => {
    try {
      await wishlistService.createOrDeleteWishlist(bookId);
      const response = await wishlistService.getWishlists();
      if (response.success === 1) {
        wishlistUser.value = response.data;
      }
    } catch (error) {
      wishlistUser.value = [];
      router.push("/login");
      setTimeout(() => {
        toast.error("please login first");
      }, 200);
    }
  };

  return {
    token,
    user,
    handleLogin,
    setToken,
    setUser,
    setWishlist,
    getUser,
    handleRegister,
    clearToken,
    me,
    isLogin,
    getToken,
    refreshAuthToken,
    logout,
    wishlistUser,
    getWishlist,
    addToWishlist,
  };
});
