<script setup lang="ts">
import { RouterView } from "vue-router";
import Loading from "@/components/LoadingResponse.vue";
import { useLoadingStore } from "@/stores/loadingStore";

const loadingStore = useLoadingStore();
</script>

<template>
  <RouterView />
  <Loading v-show="loadingStore.isLoading" />
</template>

<style scoped></style>
