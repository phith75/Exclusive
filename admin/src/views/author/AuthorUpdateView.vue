<template>
  <section id="section-content">
    <div class="content">
      <h1>Update Author</h1>
    </div>
    <div class="form-content">
      <form @submit.prevent="updateAuthor">
        <div class="form-layout">
          <InputComponent
            label="Name"
            type="text"
            id="name"
            v-model="formData.name"
            placeholder="Enter author name"
            :errorMessage="errors.name ? errors.name[0] : ''"
          />
        </div>
        <button type="submit" id="update-author">Update</button>
      </form>
    </div>
  </section>
</template>

<script setup lang="ts">
import { reactive, onMounted } from "vue";
import { useRoute } from "vue-router";
import InputComponent from "@/components/InputComponent.vue";
import authorService from "@/services/authorService";
import router from "@/router/index.js";
import type { UpdateAuthorData, FormErrors } from "@/types/index";
const route = useRoute();
const authorId = route.params.id;

// Initialize the form data
const formData = reactive<UpdateAuthorData>({
  name: "",
});

const errors = reactive<FormErrors>({
  name: null,
});

onMounted(async () => {
  try {
    const response = await authorService.getAuthorById(authorId as string);
    if (response.success) {
      Object.assign(formData, response.data);
    } else {
      console.error("Error fetching author data", response.message);
    }
  } catch (error) {
    console.error("Error fetching author data", error);
  }
});

function validateForm() {
  let isValid = true;
  if (!formData.name) {
    errors.name = ["Name is required"];
    isValid = false;
  }
  return isValid;
}

async function updateAuthor() {
  if (!validateForm()) {
    return;
  }
  try {
    Object.keys(errors).forEach((key) => {
      errors[key as keyof FormErrors] = null;
    });
    const response = await authorService.updateAuthor(
      authorId as string,
      formData
    );
    if (response.success) {
      router.push("/admin/author");
    }
  } catch (error: any) {
    if (error && error.response.status == 422 && error.response.data.data) {
      Object.assign(errors, error.response.data.data);
    }
  }
}
</script>

<style scoped>
#section-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 20px;
}

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
</style>
