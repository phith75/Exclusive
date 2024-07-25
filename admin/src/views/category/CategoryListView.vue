<template>
  <section id="section-content">
    <div class="content">
      <div class="content-header">
        <h1>List Categories</h1>
      </div>
    </div>

    <div class="action-filter">
      <div class="actions">
        <router-link :to="{ name: 'category.create' }">Create</router-link>
        <router-link :to="{ name: 'category.restore' }">Trashed</router-link>
        <button
          v-if="selectedItems.length > 0"
          @click="openBulkDeletePopup"
          class="btn-delete-many"
        >
          Delete Many
        </button>
      </div>
      <div class="filters">
        <SearchComponent v-model="searchQuery" content="categories"/>
        <div class="button-filter">
          <button class="btn-filter" @click="handleSearch">Search</button>
          <button class="btn-clear" @click="clearSearch">Clear</button>
        </div>
      </div>
    </div>
    <div class="table-component">
      <TableComponent
        :headers="categoryHeaders"
        :items="categories"
        @delete="deleteCategory"
        @selectedItems="updateSelectedItems"
        :showCheckbox="true"
        :showActions="true"
        :editRouteName="'category.update'"
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
        <p>Do you want to delete these selected categories?</p>
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
import categoryService from "@/services/categoryService";
import TableComponent from "@/components/TableComponent.vue";
import PaginationComponent from "@/components/PaginationComponent.vue";
import SearchComponent from "@/components/SearchComponent.vue";
import { toast } from "vue3-toastify";
import type { CategoryData } from "@/types/index";

// State variables
const searchQuery = ref("");
const categories = ref<CategoryData[]>([]);
const currentPage = ref(1);
const totalPages = ref(1);
const error = ref<string | null>(null);
const isLoading = ref(false);
const selectedItems = ref<number[]>([]);
const showBulkPopup = ref(false);

// Table headers
const categoryHeaders = [
  { key: "name", label: "Name" },
  { key: "image", label: "Image" },
];

// Fetch categories function
const fetchCategories = async (page = 1, keyword = "") => {
  isLoading.value = true;
  error.value = null;
  try {
    const response = await categoryService.getCategories(page, keyword);
    if (response.success === 1) {
      categories.value = response.data;
      currentPage.value = response.pagination?.current_page ?? 1;
      totalPages.value = response.pagination?.last_page ?? 1;
    } else {
      throw new Error(response.message || "Failed to fetch categories");
    }
  } catch (err: any) {
    const errorMessage = err.response?.data.errors.error_message || err.message;
    error.value = errorMessage;
    toast.error(`Error: ${errorMessage}`);
  } finally {
    isLoading.value = false;
  }
};

// Fetch categories on component mount
onMounted(() => {
  fetchCategories();
});

// Pagination functions
const goToPage = (page: number) => {
  fetchCategories(page, searchQuery.value);
};

const prevPage = () => {
  if (currentPage.value > 1) {
    fetchCategories(currentPage.value - 1, searchQuery.value);
  }
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    fetchCategories(currentPage.value + 1, searchQuery.value);
  }
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
  fetchCategories(1, searchQuery.value);
  currentQuery.value = searchQuery.value;
};

const clearSearch = () => {
  searchQuery.value = "";
  fetchCategories(1, searchQuery.value);
};

// Delete category function
const deleteCategory = async (id: number) => {
  try {
    await categoryService.deleteCategory(id);
    toast.success("Category deleted successfully");
    fetchCategories(currentPage.value, searchQuery.value);
  } catch (err: any) {
    const errorMessage = err.response?.data.errors.error_message || err.message;
    toast.error(`Delete failed: ${errorMessage}`);
  }
};

// Delete multiple categories function
const deleteManyCategories = async (selectedItems: number[]) => {
  try {
    await categoryService.deleteManyCategories(selectedItems);
    toast.success("Categories deleted successfully");
    fetchCategories(currentPage.value, searchQuery.value);
    selectedItems.length = 0;
  } catch (err: any) {
    const errorMessage = err.response?.data.errors.error_message || err.message;
    toast.error(`Delete failed: ${errorMessage}`);
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

const closeBulkPopup = () => {
  showBulkPopup.value = false;
};

// Confirm bulk delete
const confirmBulkDelete = async () => {
  await deleteManyCategories(selectedItems.value);
  closeBulkPopup();
};

// Update selected items
const updateSelectedItems = (items: number[]) => {
  selectedItems.value = items;
};
</script>
<style scoped>
.content-header {
  display: flex;
  align-items: center;
  gap: 10px;
}

</style>
