<template>
  <div class="loading-google" v-loading="loadingGoogle"></div>
</template>

<script setup lang="ts">
import { onMounted, ref } from "vue";
import { useAuthStore } from "@/stores/authStore";
import router from "@/router";
import { toast } from "vue3-toastify";

const authStore = useAuthStore();
const loadingGoogle = ref(true);
onMounted(async () => {
  try {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has("token")) {
      const token = urlParams.get("token");
      if (token) {
        authStore.setToken(token);
        await authStore.me(token);
        router.push({ name: "Homepage" });
        setTimeout(() => {
          toast.success("Login success");
        },300);
      }
    }
  } catch (error) {
    console.log(error);
  } finally {
    loadingGoogle.value = false;
  }
});
</script>

<style scoped lang="scss">
.loading-google {
  width: 100%;
  height: 100vh;
}
</style>
