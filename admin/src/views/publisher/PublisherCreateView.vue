<template>
  <section id="section-content">
    <div class="content">
      <h1>Add Publisher</h1>
    </div>
    <div class="form-content">
      <form @submit.prevent="addPublisher">
        <div class="form-layout">
          <InputComponent
            label="Name"
            type="text"
            id="name"
            v-model="formData.name"
            placeholder="Enter publisher name"
            :errorMessage="errors.name ? errors.name[0] : ''"
          />
        </div>
        <button type="submit" id="add-publisher">Submit</button>
      </form>
    </div>
  </section>
</template>
<script setup lang="ts">
import { reactive } from "vue";
import InputComponent from "@/components/InputComponent.vue";
import publisherService from "@/services/publisherService";
import router from "@/router";
import { toast } from "vue3-toastify";
import type { CreatePublisherData } from "@/types/index";

interface FormErrors {
  [key: string]: string[] | null;
  name: string[] | null;
}
const formData = reactive<CreatePublisherData>({
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
  } else {
    errors.name = null;
  }

  return isValid;
}

async function addPublisher() {
  if (!validateForm()) {
    return;
  }
  try {
    Object.keys(errors).forEach((key) => (errors[key] = null));

    const response = await publisherService.createPublisher(formData);
    if (response.success == 1) {
      Object.keys(formData).forEach(
        (key) => (formData[key as keyof CreatePublisherData] = "")
      );

      router.push({ name: "publisher.list" });
      setTimeout(() => {
        toast.success("Publisher added successfully");
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
</style>
