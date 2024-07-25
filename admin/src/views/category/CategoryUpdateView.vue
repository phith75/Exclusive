<template>
  <section id="section-content">
    <div class="content">
      <h1>Update Category</h1>
    </div>
    <div class="form-content">
      <form @submit.prevent="updateCategory">
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
            id="image"
            v-model="formData.image"
            placeholder="Enter category image"
            :errorMessage="errors.image ? errors.image[0] : ''"
          />
        </div>
        <button type="submit" id="update-category">Update</button>
      </form>
    </div>
  </section>
</template>

<script setup lang="ts">
import { reactive, onMounted } from "vue";
import { useRoute } from "vue-router";
import InputComponent from "@/components/InputComponent.vue";
import ImageUploadComponent from "@/components/ImageUploadComponent.vue";
import categoryService from "@/services/categoryService";
import router from "@/router/index.js";
import type { UpdateCategoryData } from "@/types/index";
import { toast } from "vue3-toastify";

const route = useRoute();
const categoryId = route.params.id as string;
const formData = reactive<UpdateCategoryData>({
  name: "",
  image: null,
});

// Define interface for Form errors
interface FormErrors {
  name: string[] | null;
  image: string[] | null;
}

const errors = reactive<FormErrors>({
  name: null,
  image: null,
});

onMounted(async () => {
  try {
    const response = await categoryService.getCategoryById(categoryId);
    if (response.success) {
      Object.assign(formData, response.data);
      formData.image = response.data.image;
    } else {
      console.error("Error fetching category data", response.message);
    }
  } catch (error) {
    console.error("Error fetching category data", error);
  }
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
async function updateCategory() {
  if (!validateForm()) {
    return;
  }
  try {
    Object.keys(errors).forEach((key) => {
      errors[key as keyof FormErrors] = null;
    });

    // Prepare form data for submission
    const submissionData: any = {
      name: formData.name,
    };

    // Only include the image if it is a File
    if (formData.image instanceof File) {
      submissionData.image = formData.image;
    }

    const response = await categoryService.updateCategory(
      categoryId,
      submissionData
    );
    if (response.success) {
      router.push("/admin/category");
      setTimeout(() => {
        toast.success("Category updated successfully");
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
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 100%;
  max-width: 400px; /* Adjust as necessary */
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
