<template>
  <section id="section-content">
    <div class="content">
      <div class="content-header">
        <h1>List Publishers</h1>
      </div>
    </div>
    <div class="action-filter">
      <div class="actions">
        <router-link :to="{ name: 'publisher.create' }">Create</router-link>
        <router-link :to="{ name: 'publisher.restore' }">Trashed</router-link>
        <button
          v-if="selectedItems.length > 0"
          @click="openBulkDeletePopup"
          class="btn-delete-many"
        >
          Delete Many
        </button>
      </div>

      <div class="filters">
        <SearchComponent v-model="searchQuery" content="publishers" />
        <div class="button-filter">
          <button class="btn-filter" @click="handleSearch">Search</button>
          <button class="btn-clear" @click="clearSearch">Clear</button>
        </div>
      </div>
    </div>

    <div class="table-component">
      <TableComponent
        :headers="publisherHeaders"
        :items="publishers"
        @delete="deletePublisher"
        @selectedItems="updateSelectedItems"
        :showCheckbox="true"
        :showActions="true"
        :editRouteName="'publisher.update'"
      />
    </div>

    <PaginationComponent
      v-if="totalPages > 1"
      :currentPage="currentPage"
      :totalPages="totalPages"
      @goToPage="goToPage"
      @prevPage="prevPage"
      @nextPage="nextPage"
    />

    <div class="popup-overlay" v-if="showBulkPopup">
      <div class="popup-content">
        <span class="close" @click="closeBulkPopup">&times;</span>
        <p>Do you want to delete these selected publishers?</p>
        <div class="btnDelete">
          <button class="btnConfirm btn--primary" @click="closeBulkPopup">
            Cancel
          </button>
          <button class="btnConfirm btn--danger" @click="confirmBulkDelete">
            Delete
          </button>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import publisherService from "@/services/publisherService";
import TableComponent from "@/components/TableComponent.vue";
import PaginationComponent from "@/components/PaginationComponent.vue";
import SearchComponent from "@/components/SearchComponent.vue";
import { toast } from "vue3-toastify";
import type { PublisherData } from "@/types/index";

// State variables
const searchQuery = ref("");
const publishers = ref<PublisherData[]>([]);
const currentPage = ref(1);
const totalPages = ref(1);
const error = ref<string | null>(null);
const isLoading = ref(false);
const selectedItems = ref<number[]>([]);
const showBulkPopup = ref(false);

// Table headers
const publisherHeaders = [{ key: "name", label: "Name" }];

// Fetch publishers function
const fetchPublishers = async (page = 1, keyword = "") => {
  isLoading.value = true;
  error.value = null;
  try {
    const response = await publisherService.getPublishers(page, keyword);
    if (response.success === 1) {
      publishers.value = response.data;
      currentPage.value = response.pagination?.current_page ?? 1;
      totalPages.value = response.pagination?.last_page ?? 1;
    } else {
      throw new Error(response.message || "Failed to fetch publishers");
    }
  } catch (err: any) {
    error.value = err.message;
    toast.error(`Error: ${err.message}`);
  } finally {
    isLoading.value = false;
  }
};

// Fetch publishers on component mount
onMounted(() => {
  fetchPublishers();
});

// Pagination functions
const goToPage = (page: number) => {
  fetchPublishers(page, searchQuery.value);
};

const prevPage = () => {
  if (currentPage.value > 1) {
    fetchPublishers(currentPage.value - 1, searchQuery.value);
  }
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    fetchPublishers(currentPage.value + 1, searchQuery.value);
  }
};

// Delete publisher function
const deletePublisher = async (id: number) => {
  try {
    await publisherService.deletePublisher(id);
    toast.success("Publisher deleted successfully");
    fetchPublishers(currentPage.value, searchQuery.value);
  } catch (err: any) {
    if (err.response && err.response.data.errors.error_message) {
      toast.error(err.response.data.errors.error_message);
    } else {
      toast.error(`Delete failed: ${err.message}`);
    }
  }
};

// Delete multiple publishers function
const deleteManyPublishers = async (selectedItems: number[]) => {
  try {
    await publisherService.deleteManyPublishers(selectedItems);
    toast.success("Publishers deleted successfully");
    fetchPublishers(currentPage.value, searchQuery.value);
    selectedItems.length = 0; // Clear selected items after deletion
  } catch (err: any) {
    if (err.response && err.response.data.errors.error_message) {
      toast.error(err.response.data.errors.error_message);
    } else {
      toast.error(`Delete failed: ${err.message}`);
    }
  }
};

// Open bulk delete popup
const openBulkDeletePopup = () => {
  if (selectedItems.value.length === 0) {
    toast.error("Please select items to delete");
    return;
  }
  showBulkPopup.value = true;
};

// Close bulk delete popup
const closeBulkPopup = () => {
  showBulkPopup.value = false;
};

// Confirm bulk delete
const confirmBulkDelete = async () => {
  await deleteManyPublishers(selectedItems.value);
  closeBulkPopup();
  if (publishers.value.length === 0) {
    selectedItems.value = [];
  }
};

// Update selected items
const updateSelectedItems = (items: number[]) => {
  selectedItems.value = items;
};

// Handle search submission
const currentQuery = ref("");

// Handle search submission
const handleSearch = () => {
  if (
    (currentQuery.value == "" && searchQuery.value == "") ||
    currentQuery.value == searchQuery.value
  ) {
    return;
  }
  fetchPublishers(1, searchQuery.value);
  currentQuery.value = searchQuery.value;
};

const clearSearch = () => {
  searchQuery.value = "";
  fetchPublishers(1, searchQuery.value);
};
</script>

<style scoped>
.content-header {
  display: flex;
  align-items: center;
  gap: 10px;
}
</style>
