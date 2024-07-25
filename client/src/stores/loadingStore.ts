// stores/loadingStore.js
import { defineStore } from "pinia";
import { computed, ref } from "vue";

export const useLoadingStore = defineStore("loading", () => {
  const loading = ref(0);

  const increaseLoadingCount = () => {
    loading.value++;
  };

  const decreaseLoadingCount = () => {
    if (loading.value > 0) {
      loading.value--;
    }
  };

  const isLoading = computed(() => loading.value > 0);

  return {
    loading,
    increaseLoadingCount,
    decreaseLoadingCount,
    isLoading,
  };
});
