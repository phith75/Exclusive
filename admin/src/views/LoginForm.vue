<template>
  <div class="login-window">
    <div class="login-form">
      <form @submit.prevent="login">
        <h2>Login to Account</h2>
        <p>Please enter your email and password to continue</p>
        <CustomInput
          id="email"
          v-model="loginForm.email"
          label="Email Address"
          placeholder="Enter your email address"
          :error-message="emailError"
          :is-invalid="!!emailError"
          class="form-item-email"
          @input="validateEmail"
        />
        <CustomInput
          id="password"
          v-model="loginForm.password"
          label="Password"
          type="password"
          placeholder="• • • • • •"
          :error-message="passwordError"
          :is-invalid="!!passwordError"
          @input="validatePassword"
        />
        <div class="remember">
          <input
            type="checkbox"
            id="remember-me"
            v-model="loginForm.rememberMe"
          />
          <label for="remember-me">
            <span class="checkmark"></span> Remember Password
          </label>
        </div>
        <div class="form-group">
          <button class="login-btn" id="login" :disabled="!isFormValid">
            Sign In
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive, computed, ref } from "vue";
import CustomInput from "@/components/InputComponent.vue";
import { useAuthStore } from "@/stores/authStore";
import { toast } from "vue3-toastify";

const loginForm = reactive({
  email: "",
  password: "",
  rememberMe: false,
});
const authStore = useAuthStore();
const isLoading = ref(false);
const apiError = ref("");

const validateEmailFormat = (email: string) => {
  const re =
    /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(String(email).toLowerCase());
};

const emailError = ref("");
const passwordError = ref("");

const validateEmail = () => {
  if (!loginForm.email) {
    emailError.value = "Email is required";
  } else if (!validateEmailFormat(loginForm.email)) {
    emailError.value = "Invalid email format";
  } else {
    emailError.value = "";
  }
};

const validatePassword = () => {
  if (!loginForm.password) {
    passwordError.value = "Password is required";
  } else {
    passwordError.value = "";
  }
};

const isFormValid = computed(() => !emailError.value && !passwordError.value);

const login = async () => {
  validateEmail();
  validatePassword();

  if (!isFormValid.value) return;

  try {
    isLoading.value = true;
    apiError.value = "";
    await authStore.handleLogin(loginForm);
  } catch (error: any) {
    apiError.value = error.message || "Login failed. Please try again.";
    toast.error(apiError.value);
  } finally {
    isLoading.value = false;
  }
};
</script>

<style scoped>
.login-window {
  background-image: url(../src/assets/images/background-login.png);
  background-repeat: no-repeat;
  background-size: cover;
  position: fixed;
  width: 100%;
  height: 100%;
  display: grid;
  place-items: center;
  background-color: #4880ff;
}

.login-form {
  margin: auto;
  display: flex;
  flex-direction: column;
  background-color: #ffffff;
  border-radius: 22px;
  width: 530px;
  padding: 50px 57px;
  color: #45484b;
}

.login-form h2 {
  text-align: center;
  font-weight: bold;
  font-size: 30px;
  margin-bottom: 20px;
  color: rgba(32, 34, 36, 1);
}

.login-form p {
  text-align: center;
  font-size: 16px;
  margin-bottom: 30px;
}

.login-form .form-group {
  width: 100%;
}
.form-item-email {
  margin-bottom: 10px;
}

.login-form .form-group input {
  width: 100%;
  height: 50px;
  border: 1px solid #d5d5d5;
  border-radius: 8px;
  padding: 0 20px;
  font-size: 16px;
}

#login {
  text-align: center;
  margin: 40px 0px 18px 0px;
  background-color: #4880ff;
  color: #ffffff;
  border: none;
  padding: 15px 20px;
  border-radius: 8px;
  cursor: pointer;
}

.remember {
  display: flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  user-select: none;
  margin-top: 8px;
}

.remember label {
  font-size: 14px;
  cursor: pointer;
}
.remember input[type="checkbox"] {
  position: relative;
  width: 24px;
  height: 24px;
  appearance: none;
  -webkit-appearance: none;
  outline: none;
  cursor: pointer;
  border: 1px solid #d5d5d5;
  border-radius: 6px;
  background-color: #ffffff;
}

.remember input[type="checkbox"]:checked {
  background-color: #ffffff;
}

.remember input[type="checkbox"]:checked::after {
  content: "";
  position: absolute;
  width: 7px;
  left: 5px;
  bottom: 10px;
  height: 13px;
  border: 1px solid rgba(101, 101, 101, 1);
  border-width: 0 1px 1px 0;
  transform: rotate(45deg);
  transform-origin: 0% 80%;
}
.login-btn {
  width: 318px;
  background-color: #4880ff;
  color: #ffffff;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  margin: 35px 49px !important;
}
</style>
