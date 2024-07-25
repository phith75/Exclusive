<template>
  <section id="section-content">
    <div class="content">
      <h1>Add Author</h1>
    </div>
    <div class="form-content">
      <form @submit.prevent="addAuthor">
        <div class="form-layout">
          <InputComponent
            v-model="formData.name"
            label="Name"
            type="text"
            id="name"
            placeholder="Enter author name"
            :errorMessage="errors.name ? errors.name[0] : ''"
          />
        </div>
        <button type="submit" id="add-author">Submit</button>
      </form>
    </div>
  </section>
</template>

<script setup lang="ts">
import { reactive } from "vue";
import InputComponent from "@/components/InputComponent.vue";
import authorService from "@/services/authorService";
import router from "@/router";
import { toast } from "vue3-toastify";
import type { CreateAuthorData, FormErrors } from "@/types/index";

const formData = reactive<CreateAuthorData>({
  name: "",
});

const errors = reactive<FormErrors>({
  name: null,
});

function validateForm() {
  let isValid = true;
  if (!formData.name) {
    errors.name = ["Name is required"];
    isValid = false;
  }
  return isValid;
}

async function addAuthor() {
  if (!validateForm()) {
    return;
  }

  try {
    Object.keys(errors).forEach((key) => (errors[key] = null));

    const response = await authorService.createAuthor(formData);
    if (response.success == 1) {
      Object.keys(formData).forEach(
        (key) => (formData[key as keyof CreateAuthorData] = "")
      );
      toast.success("Author added successfully");
      router.push({ name: "author.list" });
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
