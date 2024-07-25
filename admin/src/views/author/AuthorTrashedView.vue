<template>
  <section id="section-content">
    <div class="content-header">
      <h1>List Restores Authors</h1>
    </div>

    <div class="action-filter">
      <div class="actions">
        <router-link :to="{ name: 'author.list' }">Return to list</router-link>
        <button
          v-if="selectedItems.length > 0"
          @click="openBulkRestorePopup"
          class="btn-restore-many"
        >
          Restore
        </button>
      </div>

      <div class="filters">
        <SearchComponent v-model="searchQuery" content="authors" />
        <div class="button-filter">
          <button class="btn-filter" @click="handleSearch">Search</button>
          <button class="btn-clear" @click="clearSearch">Clear</button>
        </div>
      </div>
    </div>

    <div class="table-component">
      <TableComponent
        :headers="restoreHeaders"
        :items="restores"
        @selectedItems="updateSelectedItems"
        @restore="handleRestore"
        :actionType="'restore'"
        :showCheckbox="true"
        :showActions="false"
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
        <p>Do you want to restore these selected authors?</p>
        <div class="btnDelete">
          <button class="btnConfirm btn--primary" @click="closeBulkPopup">
            Cancel
          </button>
          <button class="btnConfirm btn--restore" @click="confirmBulkRestore">
            Restore
          </button>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import authorService from "@/services/authorService";
import TableComponent from "@/components/TableComponent.vue";
import PaginationComponent from "@/components/PaginationComponent.vue";
import SearchComponent from "@/components/SearchComponent.vue";
import { toast } from "vue3-toastify";
import type { AuthorData, ApiResponse } from "@/types/index";

// State variables
const searchQuery = ref("");
const restores = ref<AuthorData[]>([]);
const currentPage = ref(1);
const totalPages = ref(1);
const error = ref<string | null>(null);
const isLoading = ref(false);
const selectedItems = ref<number[]>([]);
const showBulkPopup = ref(false);

// Table headers
const restoreHeaders = [{ key: "name", label: "Name" }];

// Fetch restores function
const fetchRestores = async (page = 1, keyword = "") => {
  isLoading.value = true;
  error.value = null;
  try {
    const response: ApiResponse<AuthorData[]> = await authorService.getTrashed(
      page,
      keyword
    );
    if (response.success) {
      restores.value = response.data;
      currentPage.value = response.pagination?.current_page ?? 1;
      totalPages.value = response.pagination?.last_page ?? 1;
    } else {
      error.value = response.message || "Cannot load data";
    }
  } catch (err: any) {
    error.value = err.message;
    toast.error(`Error: ${err.message}`);
  } finally {
    isLoading.value = false;
  }
  if (restores.value.length === 0) {
    selectedItems.value = [];
  }
};

// Fetch restores on component mount
onMounted(() => {
  fetchRestores();
});

// Pagination functions
const goToPage = (page: number) => {
  fetchRestores(page, searchQuery.value);
};

const prevPage = () => {
  if (currentPage.value > 1) {
    fetchRestores(currentPage.value - 1, searchQuery.value);
  }
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    fetchRestores(currentPage.value + 1, searchQuery.value);
  }
};

// Perform restores function
const performRestores = async (selectedItems: number[]) => {
  if (selectedItems.length === 0) {
    toast.error("Please select authors to restore.");
    return;
  }
  try {
    await authorService.restore(selectedItems);
    toast.success("Authors restored successfully");
    fetchRestores(currentPage.value, searchQuery.value);
    selectedItems.length = 0; // Clear selected items after restoration
  } catch (err: any) {
    console.error("Error performing restores", err);
    toast.error(`Error performing restores: ${err.message}`);
  }
  if (restores.value.length === 0) {
    selectedItems.value = [];
  }
};

const handleRestore = (id: number) => {
  performRestores([id]);
};

// Open bulk restore popup
const openBulkRestorePopup = () => {
  if (selectedItems.value.length === 0) {
    toast.error("Please select items to restore");
    return;
  }
  showBulkPopup.value = true;
};

// Close bulk restore popup
const closeBulkPopup = () => {
  showBulkPopup.value = false;
};

// Confirm bulk restore
const confirmBulkRestore = async () => {
  await performRestores(selectedItems.value);
  closeBulkPopup();
};

// Update selected items
const updateSelectedItems = (items: number[]) => {
  selectedItems.value = items;
};

const currentQuery = ref("");

// Handle search submission
const handleSearch = () => {
  if (
    (currentQuery.value == "" && searchQuery.value == "") ||
    currentQuery.value == searchQuery.value
  ) {
    return;
  }
  fetchRestores(1, searchQuery.value);
  currentQuery.value = searchQuery.value;
};

const clearSearch = () => {
  searchQuery.value = "";
  fetchRestores(1, searchQuery.value);
};
</script>

<style scoped>
.content-header {
  display: flex;
  align-items: center;
  gap: 10px;
}
</style>
