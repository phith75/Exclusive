<template>
  <el-row justify="start" align="middle" class="reset-password-page">
    <el-col class="image-auth" :span="24" :md="13" :lg="13" :xl="13">
      <el-image :src="BackgroundAuth" />
    </el-col>
    <el-col class="form-box" :span="24" :md="11" :lg="11" :xl="11">
      <form ref="formRef" @submit.prevent="submitForm">
        <div class="form-title">
          <h1>Reset Password</h1>
          <p>Enter your new password below</p>
        </div>
        <div class="form-item">
          <input
            type="text"
            id="email"
            v-model="form.email"
            @input="handleInput('email')"
            @blur="handleBlur('email')"
            :class="{ error: errors.email }"
          />
          <label
            for="email"
            class="floating-label"
            :class="{ active: form.email }"
            >Email</label
          >
          <span v-if="errors.email" class="error-message">{{
            errors.email
          }}</span>
        </div>

        <div class="form-item">
          <input
            type="password"
            id="password"
            v-model="form.password"
            @input="handleInput('password')"
            @blur="handleBlur('password')"
            :class="{ error: errors.password }"
          />
          <label
            for="password"
            class="floating-label"
            :class="{ active: form.password }"
            >Password</label
          >
          <span v-if="errors.password" class="error-message">{{
            errors.password
          }}</span>
        </div>

        <div class="form-item">
          <input
            type="password"
            id="confirmPassword"
            v-model="form.password_confirmation"
            @input="handleInput('password_confirmation')"
            @blur="handleBlur('password_confirmation')"
            :class="{ error: errors.password_confirmation }"
          />
          <label
            for="confirmPassword"
            class="floating-label"
            :class="{ active: form.password_confirmation }"
            >Confirm Password</label
          >
          <span v-if="errors.password_confirmation" class="error-message">{{
            errors.password_confirmation
          }}</span>
        </div>

        <el-button
          type="primary"
          class="reset-password-btn"
          @click="submitForm"
          :loading="isLoading"
        >
          Reset Password
        </el-button>
      </form>
    </el-col>
  </el-row>
</template>

<script lang="ts" setup>
import { reactive, ref } from "vue";
import { useRoute } from "vue-router";
import { BackgroundAuth } from "@/assets";
import { toast } from "vue3-toastify";
import authService from "@/services/authService";
import router from "@/router";

const isLoading = ref(false);
const formRef = ref<HTMLFormElement | null>(null);
const route = useRoute();

const getTokenFromQuery = (query: any): string => {
  if (typeof query === "string") {
    const match = query.match(/token=([^&]+)/);
    return match ? decodeURIComponent(match[1]) : "";
  }
  return "";
};
const form = reactive({
  email: "",
  password: "",
  password_confirmation: "",
  token: getTokenFromQuery(route.query.token),
});

const errors = reactive({
  email: "",
  password: "",
  password_confirmation: "",
});

const validateEmail = (email: string) => {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(String(email).toLowerCase());
};

const handleInput = (field: string) => {
  validateField(field);
  if (form[field]) {
    document.getElementById(field)?.classList.add("filled");
  } else {
    document.getElementById(field)?.classList.remove("filled");
  }
};

const handleBlur = (field: string) => {
  validateField(field);
};

const validateField = (field: string) => {
  if (field === "email") {
    if (!form.email) {
      errors.email = "Email is required";
    } else if (!validateEmail(form.email)) {
      errors.email = "Invalid email address";
    } else {
      errors.email = "";
    }
  }

  if (field === "password") {
    if (!form.password) {
      errors.password = "Password is required";
    } else if (form.password.length < 8) {
      errors.password = "Password must be at least 8 characters";
    } else {
      errors.password = "";
    }
  }

  if (field === "password_confirmation") {
    if (!form.password_confirmation) {
      errors.password_confirmation = "Please confirm your password";
    } else if (form.password_confirmation !== form.password) {
      errors.password_confirmation = "Passwords do not match";
    } else {
      errors.password_confirmation = "";
    }
  }
};

const submitForm = async () => {
  validateField("email");
  validateField("password");
  validateField("password_confirmation");

  if (!errors.email && !errors.password && !errors.password_confirmation) {
    try {
      isLoading.value = true;
      await authService.resetPassword(
        form.email,
        form.password,
        form.password_confirmation,
        form.token
      );
      toast.success("Password reset successful");
      setTimeout(() => {
        router.push({ name: "login" });
      }, 500);
    } catch (error) {
      toast.error("Failed to reset password,the link may have expired");
    } finally {
      isLoading.value = false;
    }
  }
};
</script>

<style lang="scss" scoped>
.reset-password-page {
  margin: 60px 0 140px 0;

  .image-auth {
    width: 100%;
    .el-image {
      width: 100%;
    }
  }

  .form-box {
    margin: 0 auto;
    max-width: 371px;

    .form-title {
      margin-bottom: 20px;

      h1 {
        font-size: 30px;
        font-weight: 500;
        margin-bottom: 10px;
      }

      p {
        margin-bottom: 32px;
        font-size: 14px;
      }
    }

    .form-item {
      display: flex;
      flex-direction: column;
      position: relative;
      margin-bottom: 40px;

      input {
        width: 100%;
        border: none;
        border-bottom: 1px solid #8a8a8b;
        outline: none;
        font-size: 16px;
        padding: 8px 0;
        background-color: transparent;
      }

      .floating-label {
        position: absolute;
        top: 8px;
        left: 0;
        font-size: 16px;
        color: #999;
        pointer-events: none;
        transition: 0.2s ease all;
      }

      .active {
        top: -25px;
      }

      input:focus ~ .floating-label {
        color: #ff6b00;
      }

      input.error {
        border-bottom: 1px solid #ff0000;
      }

      .error-message {
        color: #ff0000;
        font-size: 14px;
        position: absolute;
        bottom: -22px;
      }
    }

    .reset-password-btn {
      background-color: #ff6b00;
      border-color: #ff6b00;
      color: #fff;
      margin: 40px 0;
      width: 100%;
      height: 56px;

      &:hover {
        background-color: #ff5a00;
        border-color: #ff5a00;
      }
    }
  }
}
</style>
