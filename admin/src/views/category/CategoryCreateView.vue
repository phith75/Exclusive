<template>
  <section id="section-content">
    <div class="content">
      <h1>Add Category</h1>
    </div>
    <div class="form-content">
      <form @submit.prevent="addCategory">
        <div class="form-layout">
          <InputComponent
            label="Name"
            type="text"
            id="name"
            v-model="formData.name"
            placeholder="Enter category name"
            :errorMessage="errors.name ? errors.name[0] : ''"
          />
        </div>
        <div class="form-layout">
          <ImageUploadComponent
            label="Image"
            id="category-image-upload"
            v-model="formData.image"
            placeholder="Enter category image"
            :errorMessage="errors.image ? errors.image[0] : ''"
          />
        </div>
        <button type="submit" id="add-category">Submit</button>
      </form>
    </div>
  </section>
</template>

<script setup lang="ts">
import { reactive } from "vue";
import InputComponent from "@/components/InputComponent.vue";
import ImageUploadComponent from "@/components/ImageUploadComponent.vue";
import categoryService from "@/services/categoryService";
import router from "@/router";
import { toast } from "vue3-toastify";
import type { CreateCategoryData } from "@/types/index";
import type { FormErrors } from "@/types/index";

const formData = reactive<CreateCategoryData>({
  name: "",
  image: null,
});

const errors = reactive<FormErrors>({
  name: null,
  image: null,
});

function validateForm() {
  let isValid = true;
  if (!formData.name) {
    errors.name = ["Name is required"];
    isValid = false;
  } else {
    errors.name = null;
  }
  if (!formData.image) {
    errors.image = ["Image is required"];
    isValid = false;
  } else {
    errors.image = null;
  }
  return isValid;
}

async function addCategory() {
  if (!validateForm()) {
    return;
  }
  try {
    Object.keys(errors).forEach((key) => (errors[key] = null));

    const response = await categoryService.createCategory(formData);
    if (response.success) {
      Object.keys(formData).forEach(
        (key) => (formData[key as keyof CreateCategoryData] = "")
      );
      router.push({ name: "category.list" });
      setTimeout(() => {
        toast.success("Category added successfully");
      }, 200);
    }
  } catch (error: any) {
    if (error && error.response.status == 422 && error.response.data.data) {
      Object.assign(errors, error.response.data.data);
    }
  }
}
</script>

<style scoped>
.form-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 100%;
}

.form-layout {
  margin-bottom: 10px;
  display: flex;
  flex-direction: column;
  width: 100%;
  max-width: 400px; 
}

button {
  margin-top: 20px;
  padding: 10px 20px;
  border: none;
  background-color: #4caf50;
  color: white;
  font-size: 16px;
  cursor: pointer;
  border-radius: 4px;
}

button:hover {
  background-color: #45a049;
}
#category-image-upload.upload-group {
  margin-bottom: 20px;
  width: 100%;
}

#category-image-upload.upload-group input {
  width: 100%;
  height: 50px;
  border: 1px solid #d5d5d5;
  border-radius: 8px;
  padding: 0 20px;
  font-size: 16px;
}

#category-image-upload .upload-error-message {
  color: red;
  font-size: 12px;
  margin-top: 5px;
}

#category-image-upload.upload-group input.error {
  outline: 1px solid red;
}

#category-image-upload .upload-image-preview {
  margin-top: 10px;
}

#category-image-upload .upload-image-preview img {
  max-width: 100%;
  max-height: 200px;
}
</style>
