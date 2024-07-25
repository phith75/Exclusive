<template>
  <label class="required" :for="id">{{ label }}</label>
  <div :class="['upload-group', { error: errorMessage }]">
    <div class="upload-content">
      <div class="upload-image-preview">
        <div class="upload-box" @click="triggerFileInput">
          <span>+</span>
          <input
            type="file"
            ref="fileInputRef"
            @change="handleFileChange"
            multiple
            :accept="allowedFormats.join(',')"
          />
        </div>
        <div
          v-for="(url, index) in imageUrls"
          :key="index"
          class="image-preview"
        >
          <img :src="url" alt="Image Preview" />
          <span class="remove-image" @click="removeImage(index)">×</span>
        </div>
      </div>
    </div>
  </div>
  <span v-if="errorMessage" class="upload-error-message">{{
    errorMessage
  }}</span>
</template>

<script setup lang="ts">
import { ref, watch, computed, toRefs } from "vue";

interface ImageObject {
  image: string;
}

const props = defineProps({
  modelValue: {
    type: Array as () => (File | ImageObject)[],
    default: () => [],
  },
  label: String,
  id: String,
  placeholder: String,
  errorMessage: String,
  maxFileSize: {
    type: Number,
    default: 2 * 1024 * 1024, // 2MB default
  },
  allowedFormats: {
    type: Array as () => string[],
    default: () => [
      "image/jpeg",
      "image/png",
      "image/gif",
      "image/webp",
      "image/jpg",
    ],
  },
});

const emit = defineEmits(["update:modelValue", "error"]);

const { modelValue, maxFileSize, allowedFormats } = toRefs(props);
const imageUrls = ref<string[]>([]);
const fileInputRef = ref<HTMLInputElement | null>(null);
const errorMessage = ref<string | null>(null);

const localModelValue = computed({
  get: () => modelValue.value,
  set: (value) => emit("update:modelValue", value),
});

const getImageUrl = (imagePath: string) => {
  if (imagePath.startsWith("https")) {
    return imagePath;
  } else {
    return `${import.meta.env.VITE_IMAGE_URL}${imagePath}`;
  }
};
watch(
  () => props.errorMessage,
  (newErrorMessage) => {
    errorMessage.value = newErrorMessage;
  }
);

watch(
  () => localModelValue.value,
  (newFiles: (File | ImageObject)[]) => {
    imageUrls.value = [];
    newFiles.forEach((file) => {
      if ((file as ImageObject).image) {
        imageUrls.value.push(getImageUrl((file as ImageObject).image));
      } else if (file instanceof File) {
        const reader = new FileReader();
        reader.onload = (e) => {
          imageUrls.value.push(e.target?.result as string);
        };
        reader.readAsDataURL(file);
      }
    });
  },
  { immediate: true }
);

function validateFile(file: File) {
  if (!allowedFormats.value.includes(file.type)) {
    errorMessage.value = `Invalid file format. Allowed formats: ${allowedFormats.value.join(", ")}`;
    return false;
  }
  if (file.size > maxFileSize.value) {
    errorMessage.value = `File size exceeds the limit of ${(maxFileSize.value / (1024 * 1024)).toFixed(2)} MB`;
    return false;
  }
  errorMessage.value = null;
  return true;
}

function handleFileChange(event: Event) {
  const target = event.target as HTMLInputElement | null;
  if (target && target.files) {
    const files = Array.from(target.files);
    const validFiles = files.filter(validateFile);
    if (validFiles.length !== files.length) {
      emit("error", errorMessage.value);
    }
    const updatedFiles = [...localModelValue.value, ...validFiles];
    localModelValue.value = updatedFiles;
  }
}

function removeImage(index: number) {
  const updatedFiles = localModelValue.value.slice();
  updatedFiles.splice(index, 1);
  localModelValue.value = updatedFiles;
}

function triggerFileInput() {
  fileInputRef.value?.click();
}
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
  display: flex;
  margin-top: 4px;
  flex-wrap: wrap;
  gap: 10px;
  align-items: flex-start;
}

.upload-group.error {
  border-color: red;
}

.upload-content {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  align-items: flex-start;
  width: 100%;
}

.upload-box {
  width: 100px;
  height: 100px;
  border: 2px dashed #d5d5d5;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 24px;
  position: relative;
}

.upload-box input {
  display: none;
}

.upload-image-preview {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  align-items: flex-start;
  flex-grow: 1;
}

.upload-placeholder {
  color: #999;
  font-size: 16px;
  margin-top: 10px;
}

.image-preview {
  width: 100px;
  height: 100px;
  overflow: hidden;
  border: 1px solid #d5d5d5;
  border-radius: 8px;
  position: relative;
}

.image-preview img {
  width: 100%;
  height: 100%;
  object-fit: cover;
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

.upload-error-message {
  color: red;
  font-size: 12px;
  margin-top: 5px;
}
.required:after {
  content: "*";
  color: red;
  margin-left: 4px;
}
</style>
