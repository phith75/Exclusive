<template>
  <div class="form-group">
    <label class="required" :for="stringId">{{ label }}</label>
    <select
      :id="stringId"
      v-model="localValue"
      :class="['select-control', selectClass, { error: errorMessage }]"
    >
      <option value="" default>Select an {{ label }}</option>
      <option
        v-for="option in options"
        :key="option.value"
        :value="option.value"
      >
        {{ option.label }}
      </option>
    </select>
    <span v-if="errorMessage" class="error-message">{{ errorMessage }}</span>
  </div>
</template>

<script setup lang="ts">
import {
  computed,
  toRefs,
  defineProps,
  defineEmits,
  type PropType,
  watch,
  onMounted,
} from "vue";
import type { Option } from "@/types/index";

const props = defineProps({
  modelValue: {
    type: [String, Number] as PropType<string | number>,
    required: true,
  },
  label: {
    type: String,
    required: false,
  },
  id: {
    type: [Number, String] as PropType<number | string>,
    required: true,
  },
  options: {
    type: Array as PropType<Option[]>,
    required: true,
  },
  errorMessage: {
    type: String,
    required: false,
  },
});

const emit = defineEmits(["update:modelValue"]);
const { modelValue, id } = toRefs(props);
const localValue = computed({
  get: () => modelValue.value,
  set: (value) => emit("update:modelValue", value),
});
const stringId = computed(() => (id.value ? id.value.toString() : ""));

const getOptionClass = (value: string | number) => {
  const option = props.options.find((opt) => opt.value === value);
  if (option) {
    return `select-${option.label.toLowerCase()}`;
  }
  return "";
};

const selectClass = computed(() => getOptionClass(localValue.value));

watch(localValue, (newVal) => {
  const className = getOptionClass(newVal);
  if (stringId.value) {
    const selectElement = document.getElementById(stringId.value);
    if (selectElement) {
      selectElement.className = `select-control ${className}`;
    }
  }
});

onMounted(() => {
  const initialClass = getOptionClass(localValue.value);
  if (stringId.value) {
    const selectElement = document.getElementById(stringId.value);
    if (selectElement) {
      selectElement.className = `select-control ${initialClass}`;
    }
  }
});
</script>

<style scoped>
.form-group {
  width: 100%;
  position: relative;
  padding-bottom: 25px;
}

.select-control {
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

.select-control.error {
  outline: 1px solid red;
}
.required:after {
  content: "*";
  color: red;
  margin-left: 4px;
}
</style>
