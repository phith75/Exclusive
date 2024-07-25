<template>
  <el-row justify="start" align="middle" class="login-page">
    <el-col class="image-auth" :span="24" :md="13" :lg="13" :xl="13">
      <el-image :src="BackgroundAuth" />
    </el-col>
    <el-col class="form-box" :span="24" :md="11" :lg="11" :xl="11">
      <form ref="formRef" @submit.native.prevent="submitForm">
        <div class="form-title">
          <h1>Log in to Exclusive</h1>
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
        <div class="btn-group">
          <el-button
            type="primary"
            class="create-account-btn"
            @keyup.enter.native="submitForm"
            @click="submitForm"
            :loading="isLoading"
          >
            Login
          </el-button>
          <span class="forgot-password">
            <a href="/forgot-password">Forget password?</a>
          </span>
        </div>
      </form>
    </el-col>
  </el-row>
</template>

<script lang="ts" setup>
import { reactive, ref } from "vue";
import { BackgroundAuth } from "@/assets";
import { useAuthStore } from "@/stores/authStore";
const isLoading = ref(false);
const authStore = useAuthStore();
const formRef = ref<HTMLFormElement | null>(null);

const form = reactive({
  email: "",
  password: "",
});

const errors = reactive({
  email: "",
  password: "",
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
      errors.password = "Password must be at least 8 characters long";
    } else {
      errors.password = "";
    }
  }
};

const submitForm = async () => {
  validateField("email");
  validateField("password");

  if (!errors.email && !errors.password) {
    try {
      isLoading.value = true;
      await authStore.handleLogin({
        email: form.email,
        password: form.password,
      });
    } catch (error: any) {
      if (error.response.status == 401) {
        errors.email = "Invalid email or password";
      }
    } finally {
      isLoading.value = false;
    }
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

    .btn-group {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .create-account-btn {
      background-color: #db4444;
      border-color: #db4444;
      color: #fff;
      width: 143px;
      height: 56px;

      &:hover {
        background-color: #ff5a00;
        border-color: #ff5a00;
      }
    }

    .google-signup-btn {
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
      display: block;
      text-align: center;
      font-size: 14px;
      a {
        color: #db4444;
      }
    }
  }
}
</style>
