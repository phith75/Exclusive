<template>
  <el-row justify="start" align="middle" class="login-page">
    <el-col class="image-auth" :span="24" :md="13" :lg="13" :xl="13">
      <el-image :src="BackgroundAuth" />
    </el-col>
    <el-col class="form-box" :span="24" :md="11" :lg="11" :xl="11">
      <form ref="formRef" @submit.native.prevent="submitForm">
        <div class="form-title">
          <h1>Submit your email</h1>
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
          >
            Email
          </label>
          <span v-if="errors.email" id="emailError" class="error-message">
            {{ errors.email }}
          </span>
        </div>
        <el-button
          type="primary"
          class="create-account-btn"
          :loading="isLoading"
          @click="submitForm"
          @keyup.enter.native="submitForm"
        >
          Send link
        </el-button>
      </form>
    </el-col>
  </el-row>
</template>
<script lang="ts" setup>
import { reactive, ref } from "vue";
import { BackgroundAuth } from "@/assets";
import { useAuthStore } from "@/stores/authStore";
import { toast } from "vue3-toastify";
import authService from "@/services/authService";

const formRef = ref<HTMLFormElement | null>(null);
const authStore = useAuthStore();

const form = reactive({
  email: "",
});

const errors = reactive({
  email: "",
});

const isLoading = ref(false);

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
};

const submitForm = async () => {
  validateField("email");

  if (!errors.email) {
    isLoading.value = true;
    try {
      await authService.forgotPassword(form.email);
      toast.success("If your email exist, we send you a link to reset your password");
    } catch (error) {
      toast.success("If your email exist, we send you a link to reset your password");
    } finally {
      isLoading.value = false;
    }
  }
};
</script>
<style lang="scss" scoped>
h1 {
  font-size: 30px;
  font-weight: 500;
  margin-bottom: 10px;
}
.form-title {
  margin-bottom: 40px;
}
.login-page {
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

    .form-item {
      display: flex;
      flex-direction: column;
      position: relative;

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
      margin: 40px 0;
      width: 100%;
      height: 56px;

      &:hover {
        background-color: #ff5a00;
        border-color: #ff5a00;
      }
    }

    .forgot-password {
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
