<template>
  <el-row justify="start" align="middle" class="login-page">
    <el-col class="image-auth" :span="24" :md="13" :lg="13" :xl="13">
      <el-image :src="BackgroundAuth" />
    </el-col>
    <el-col class="form-box" :span="24" :md="11" :lg="11" :xl="11">
      <form ref="formRef" @submit.native.prevent="submitForm">
        <div class="form-title">
          <h1>Create an account</h1>
          <p>Enter your details below</p>
        </div>
        <div class="form-item">
          <input
            type="text"
            id="email"
            v-model="form.email"
            @input="handleInput('email')"
            @focus="handleInput('email')"
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
            @focus="handleInput('password')"
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
            v-model="form.confirmPassword"
            @input="handleInput('confirmPassword')"
            @focus="handleInput('confirmPassword')"
            @blur="handleBlur('confirmPassword')"
            :class="{ error: errors.confirmPassword }"
          />
          <label
            for="confirmPassword"
            class="floating-label"
            :class="{ active: form.confirmPassword }"
            >Confirm Password</label
          >
          <span v-if="errors.confirmPassword" class="error-message">{{
            errors.confirmPassword
          }}</span>
        </div>
        <el-button
          type="primary"
          class="create-account-btn"
          @keyup.enter.native="submitForm"
          :loading="isLoading"
          @click="submitForm"
        >
          Create Account
        </el-button>
        <button :loading="isLoadingGoogle" type="button" @click="googleLogin" class="google-signup-btn">
          <img :src="GoogleIcon" class="google-icon" alt="Google Icon" />Sign up
          with Google
        </button>
        <span class="forgot-password">
          Already have account?
          <router-link to="/login"> Log in</router-link>
        </span>
      </form>
    </el-col>
  </el-row>
</template>

<script lang="ts" setup>
import { reactive, ref } from "vue";
import { BackgroundAuth, GoogleIcon } from "@/assets";
import authService from "@/services/authService";
import { useAuthStore } from "@/stores/authStore";

const authStore = useAuthStore();
const formRef = ref<HTMLFormElement | null>(null);
const isLoading = ref(false);
const isLoadingGoogle = ref(false);
const form = reactive({
  email: "",
  password: "",
  confirmPassword: "",
});

const errors = reactive({
  email: "",
  password: "",
  confirmPassword: "",
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
    } else {
      errors.password = "";
    }
  }

  if (field === "confirmPassword") {
    if (!form.confirmPassword) {
      errors.confirmPassword = "Confirm Password is required";
    } else if (form.confirmPassword !== form.password) {
      errors.confirmPassword = "Passwords do not match";
    } else {
      errors.confirmPassword = "";
    }
  }
};

const submitForm = async () => {
  validateField("email");
  validateField("password");
  validateField("confirmPassword");

  if (!errors.email && !errors.password && !errors.confirmPassword) {
    try {
      isLoading.value = true;
      await authStore.handleRegister({
        email: form.email,
        password: form.password,
        confirmPassword: form.confirmPassword,
      });
    } catch (error: any) {
      if (error.response.status == 401) {
        errors.email = "Invalid email or password";
      }
      if (error.response.status == 422) {
        errors.email = error.response.data.errors.error_message;
      }
    } finally {
      isLoading.value = false;
    }
  }
};

const googleLogin = async () => {
  try {
    isLoadingGoogle.value = true;
    const googleUrl = await authService.googleLogin();
    window.location.href = googleUrl.data.url;
  } catch (error: any) {
  } finally {
    isLoadingGoogle.value = false;
  }
};
</script>

<style lang="scss" scoped>
.login-page {
  margin: 60px 0 140px 0;

  .image-auth {
    width: 100%;
    .el-image {
      width: 100%;
    }
  }

  .form-box {
    flex: 1;
    max-width: 371px;
    margin: 0 auto;

    form {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

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

    .create-account-btn {
      background-color: #ff6b00;
      border-color: #ff6b00;
      color: #fff;
      margin-top: 20px;
      width: 100%;
      height: 56px;

      &:hover {
        background-color: #ff5a00;
        border-color: #ff5a00;
      }
    }

    .google-signup-btn {
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 100%;
      height: 56px;
      border: 1px solid #dcdfe6;
      color: #666;

      &:hover {
        border-color: #c0c4cc;
        color: #444;
      }

      .google-icon {
        margin-right: 10px;

        img {
          width: 20px;
        }
      }
    }

    .forgot-password {
      margin-top: 20px ;
      display: block;
      text-align: center;
      font-size: 14px;
      color: #666;
      a {
        color: #ff6b00;
      }
    }
  }
}
</style>
