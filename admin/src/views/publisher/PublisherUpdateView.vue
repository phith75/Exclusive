<template>
  <section id="section-content">
    <div class="content">
      <h1>Update Publisher</h1>
    </div>
    <div class="form-content">
      <form @submit.prevent="updatePublisher">
        <div class="form-layout">
          <InputComponent
            label="Name"
            type="text"
            id="name"
            v-model="JsonData.name"
            placeholder="Enter publisher name"
            :errorMessage="errors.name ? errors.name[0] : ''"
          />
        </div>
        <button type="submit" id="update-publisher">Update</button>
      </form>
    </div>
  </section>
</template>

<script setup lang="ts">
import { reactive, onMounted } from "vue";
import { useRoute } from "vue-router";
import InputComponent from "@/components/InputComponent.vue";
import publisherService from "@/services/publisherService";
import router from "@/router/index.ts";
import type { UpdatePublisherData } from "@/types/index";
import { toast } from "vue3-toastify";
const route = useRoute();
const publisherId = route.params.id;

const JsonData = reactive<UpdatePublisherData>({
  name: "",
});

interface FormErrors {
  name: string[] | null;
}

const errors = reactive<FormErrors>({
  name: null,
});

onMounted(async () => {
  try {
    const response = await publisherService.getPublisherById(
      publisherId as string
    );
    if (response.success) {
      Object.assign(JsonData, response.data);
    } else {
      console.error("Error fetching publisher data", response.message);
    }
  } catch (error) {
    console.error("Error fetching publisher data", error);
  }
});

async function updatePublisher() {
  try {
    Object.keys(errors).forEach((key) => {
      errors[key as keyof FormErrors] = null;
    });
    const response = await publisherService.updatePublisher(
      publisherId as string,
      JsonData
    );
    if (response.success) {
      router.push("/admin/publisher");
      setTimeout(() => {
        toast.success("Publisher updated successfully");
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
