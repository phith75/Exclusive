import apiRequest from "@/services/apiService";
import type { LoginResponse, updateProfile, User } from "@/types";

const authService = {
  async login(email: string, password: string): Promise<LoginResponse> {
    return await apiRequest({
      method: "post",
      url: "/auth/login",
      data: { email, password },
    });
  },

  async refreshToken(): Promise<LoginResponse> {
    return await apiRequest({
      method: "post",
      url: "/auth/refresh",
    });
  },

  async register(email: string, password: string, password_confirmation: string): Promise<LoginResponse> {
    return await apiRequest({
      method: "post",
      url: "/auth/register",
      data: { email, password, password_confirmation },
    });
  },

  async logout(): Promise<void> {
    return await apiRequest({
      method: "post",
      url: "/auth/logout",
    });
  },

  async me(token: string): Promise<User> {
    try {
      return await apiRequest({
        method: "get",
        url: "/auth/me",
      });
    } catch (error) {
      console.error("Error getting user info:", error);
      throw error;
    }
  },

  async forgotPassword(email: string): Promise<void> {
    try {
      return await apiRequest({
        method: "post",
        url: "/forgot-password",
        data: { email },
      });
    } catch (error) {
      console.error("Error in password recovery:", error);
      throw error;
    }
  },

  async resetPassword(
    email: string,
    password: string,
    password_confirmation: string,
    token: string
  ): Promise<LoginResponse> {
    try {
      return await apiRequest({
        method: "put",
        url: "/reset-password",
        data: {
          email,
          password,
          password_confirmation,
          token,
        },
      });
    } catch (error) {
      console.error("Error resetting password:", error);
      throw error;
    }
  },

  async changePassword(
    current_password: string,
    new_password: string,
    new_password_confirmation: string
  ): Promise<LoginResponse> {
    try {
      return await apiRequest({
        method: "put",
        url: "/auth/change-password",
        data: {
          current_password,
          new_password,
          new_password_confirmation,
        },
      });
    } catch (error) {
      console.error("Error changing password:", error);
      throw error;
    }
  },

  async updateProfile(
    name?: string,
    phone?: string,
    gender?: number,
    address?: string,
    province_id?: string,
    district_id?: string,
    ward_id?: string,
    date_of_birth?: string
  ): Promise<updateProfile> {
    try {
      return await apiRequest({
        method: "put",
        url: "/auth/update-profile",
        data: {
          name,
          phone,
          gender,
          address,
          province_id,
          district_id,
          ward_id,
          date_of_birth,
        },
      });
    } catch (error) {
      console.error("Error updating profile:", error);
      throw error;
    }
  },

  async googleLogin(): Promise<any> {
    try {
      return await apiRequest({
        method: "get",
        url: "/auth/google",
      });
    } catch (error) {
      console.error("Error logging in with Google:", error);
      throw error;
    }
  },
  async googleLoginCallback(): Promise<any> {
    try {
      return await apiRequest({
        method: "get",
        url: "/auth/google/callback",
      });
    } catch (error) {
      console.error("Error logging in with Google:", error);
      throw error;
    }
  },
};

export default authService;
