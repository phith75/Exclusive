<template>
  <div class="form-group">
    <label class="required" :for="id">{{ label }}</label>
    <input
      :type="type"
      :id="id"
      v-model="localValue"
      @change="handleFileChange($event)"
      :class="{ error: errorMessage }"
      :placeholder="placeholder"
    />
    <span v-if="errorMessage" class="error-message">{{ errorMessage }}</span>
  </div>
</template>

<script setup lang="ts">
import { computed, toRefs } from "vue";

const props = defineProps({
  modelValue: {
    type: [String, Number, File],
    default: "",
  },
  label: String,
  type: {
    type: String,
    default: "text",
  },
  id: String,
  placeholder: String,
  errorMessage: String,
});

const emit = defineEmits(["update:modelValue"]);
const { modelValue } = toRefs(props);

const localValue = computed({
  get: () => modelValue.value,
  set: (value) => emit("update:modelValue", value),
});

function handleFileChange(event: Event) {
  const target = event.target as HTMLInputElement | null;
  if (target && target.files) {
    const file = target.files[0];
    emit("update:modelValue", file);
  }
}
</script>

<style scoped>
.form-group {
  width: 100%;
  position: relative;
  padding-bottom: 25px; 
}

.form-group input {
  width: 100%;
  height: 50px;
  border: 1px solid #d5d5d5;
  border-radius: 8px;
  padding: 0 20px;
  font-size: 16px;
}

.error-message {
  color: red;
  font-size: 12px;
  margin-top: 5px;
  position: absolute;
  bottom: 5px; 
}

.form-group input.error {
  outline: 1px solid red;
}
.required:after {
  content: '*';
  color: red;
  margin-left: 4px;
}

</style>