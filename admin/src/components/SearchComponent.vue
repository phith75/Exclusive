<template>
  <div class="header-left-search header-left-search-menu">
    <img src="@/assets/images/search.png" alt="search" />
    <input
      type="text"
      id="input-search"
      :placeholder="'Search ' + content"
      v-model="internalSearchText"
      @input="onInput"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, watch, defineEmits, defineProps } from "vue";

const props = defineProps<{
  modelValue: string;
  content: string;
}>();

const emit = defineEmits<{
  (e: "update:modelValue", value: string): void;
  (e: "search", value: string): void;
}>();

const internalSearchText = ref(props.modelValue);

watch(
  () => props.modelValue,
  (newVal) => {
    internalSearchText.value = newVal;
  }
);

const onInput = () => {
  emit("update:modelValue", internalSearchText.value);
  emit("search", internalSearchText.value);
};
</script>

<style scoped>
.header-left-nav-icon img {
  width: 20px;
  cursor: pointer;
}
.header-left-search-menu {
  background-color: #fff;
  border-radius: 20px;
  width: 253px;
  height: 38px;
  display: flex;
  gap: 12px;
  border: 1px solid #d5d5d5;
  align-items: center;
  padding: 12px 16px;
}

.header-left-search-menu input {
  border: none;
  outline: none;
  background-color: transparent;
}

.header-left-search-menu img {
  width: 20px;
}

.header-left-search {
  display: flex;
  align-items: center;
  padding: 10px 16px;
  gap: 10px;
  border-radius: 20px;
}

.header-left-search img {
  width: 20px;
}
</style>
