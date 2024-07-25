<template>
  <label :for="id" class="upload-label required">{{ label }}</label>

  <div
    class="upload-group"
    :class="{ 'is-dragover': isDragOver, error: errorMessage }"
    @dragover.prevent="isDragOver = true"
    @dragleave.prevent="isDragOver = false"
    @drop.prevent="handleDrop"
    @click="triggerFileInput"
  >
    <input
      type="file"
      :id="id"
      @change="handleFileChange"
      :class="{ error: errorMessage }"
      :placeholder="placeholder"
      accept="image/*"
    />
    <div v-if="imageUrl" class="upload-image-preview">
      <img :src="getImageUrl(imageUrl)" alt="Image Preview" />
      <span class="remove-image" @click.stop="removeImage">×</span>
    </div>
    <div v-else class="upload-placeholder">
      Drag & drop an image here, or click to select one
    </div>
  </div>
  <span v-show="errorMessage" class="upload-error-message">{{
    errorMessage
  }}</span>
</template>

<script setup lang="ts">
import { ref, watch, toRefs, computed, nextTick, onMounted } from "vue";

const props = defineProps({
  modelValue: {
    type: [File, String],
    default: null,
  },
  label: String,
  id: String,
  placeholder: String,
  errorMessage: String,
  maxFileSize: {
    type: Number,
    default: 2 * 1024 * 1024,
  },
  allowedFormats: {
    type: Array as () => string[],
    default: () => [
      "image/jpeg",
      "image/png",
      "image/gif",
      "image/webp",
      "image/svg",
      "image/jpg",
    ],
  },
  required: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(["update:modelValue", "error"]);

const { modelValue, maxFileSize, allowedFormats } = toRefs(props);
const localValue = computed({
  get: () => modelValue.value,
  set: (value) => emit("update:modelValue", value),
});

const imageUrl = ref<string | null>(null);
const isDragOver = ref(false);
const errorMessage = ref<string | null>(null);
const initialized = ref(false);

watch(
  () => props.errorMessage,
  (newErrorMessage) => {
    if (newErrorMessage) {
      errorMessage.value = newErrorMessage;
    }
  }
);
const getImageUrl = (imagePath: any) => {
  if (imagePath.startsWith("https")) {
    return imagePath;
  } else if (imagePath.startsWith("data")) {
    return imagePath;
  } else {
    return `${import.meta.env.VITE_IMAGE_URL}${imagePath}`;
  }
};

watch(
  () => modelValue.value,
  (newFile) => {
    if (initialized.value) {
      nextTick(() => {
        if (typeof newFile == "string") {
          imageUrl.value = newFile;
        } else if (newFile instanceof File) {
          const reader = new FileReader();
          reader.onload = (e) => {
            imageUrl.value = e.target?.result as string;
          };
          reader.readAsDataURL(newFile);
        } else {
          imageUrl.value = null;
        }
      });
    } else {
      initialized.value = true;
    }
  },
  { immediate: true }
);

function validateFile(file: File) {
  if (!allowedFormats.value.includes(file.type)) {
    errorMessage.value = `Invalid file format. Allowed formats: ${allowedFormats.value.join(", ")}`;
    return false;
  }
  if (file.size > maxFileSize.value) {
    errorMessage.value = `File size exceeds the limit of ${maxFileSize.value / (1024 * 1024)} MB`;
    return false;
  }
  errorMessage.value = null;
  return true;
}

function handleFileChange(event: Event) {
  const target = event.target as HTMLInputElement | null;
  if (target && target.files && target.files.length > 0) {
    const file = target.files[0];
    if (validateFile(file)) {
      localValue.value = file;
      errorMessage.value = null;
    } else {
      emit("error", errorMessage.value);
    }
  } else if (props.required) {
    errorMessage.value = "File is required";
  }
}

function handleDrop(event: DragEvent) {
  isDragOver.value = false;
  if (event.dataTransfer?.files && event.dataTransfer.files.length > 0) {
    const file = event.dataTransfer.files[0];
    if (validateFile(file)) {
      localValue.value = file;
      errorMessage.value = null;
    } else {
      emit("error", errorMessage.value);
    }
  } else if (props.required) {
    errorMessage.value = "File is required";
  }
}

function removeImage() {
  imageUrl.value = null;
  localValue.value = null;
}

function triggerFileInput() {
  const fileInput = document.getElementById(props.id as string);
  if (fileInput) {
    (fileInput as HTMLInputElement).click();
  }
}
onMounted(() => {
  imageUrl.value = modelValue.value as string;
});
</script>

<style scoped>
.upload-group {
  width: 100%;
  border: 2px dashed #d5d5d5;
  border-radius: 8px;
  padding: 20px;
  text-align: center;
  transition:
    background-color 0.3s,
    border-color 0.3s;
  cursor: pointer;
}

.upload-group.is-dragover {
  background-color: #f0f0f0;
}

.upload-group.error {
  border-color: red;
}

.upload-group input {
  display: none;
}

.upload-error-message {
  margin-top: 5px;
  color: red;
  font-size: 12px;
  min-height: 10px;
}

.upload-group input.error {
  outline: 1px solid red;
}

.upload-image-preview {
  position: relative;
  margin-top: 10px;
  display: inline-block;
}

.upload-image-preview img {
  max-width: 100%;
  max-height: 200px;
  border: 1px solid #d5d5d5;
  border-radius: 8px;
}

.remove-image {
  position: absolute;
  top: 5px;
  right: 5px;
  background: rgba(0, 0, 0, 0.6);
  color: white;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 14px;
}

.upload-placeholder {
  color: #999;
  font-size: 16px;
  margin-top: 10px;
}

.upload-label {
  display: block;
  color: #606060;
  margin-bottom: 4px;
}
.required:after {
  content: "*";
  color: red;
  margin-left: 4px;
}
</style>
