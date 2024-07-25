<template>
  <section id="section-content">
    <div class="content">
      <div class="content-header">
        <h1>List Trashed Reviews</h1>
      </div>
    </div>

    <div class="action-filter">
      <div class="actions">
        <router-link :to="{ name: 'review.list' }">Return to list</router-link>
        <button
          v-if="selectedItems.length > 0"
          @click="openBulkRestorePopup"
          class="btn-restore-many"
        >
          Restore Many
        </button>
      </div>
      <div class="filters">
        <div class="filter-left">
          <div class="group-select">
            <label for="">Select Status</label>
            <select v-model="selectedRating">
              <option value="">All Ratings</option>
              <option v-for="star in 5" :key="star" :value="star">
                {{ star }} Star{{ star > 1 ? "s" : "" }}
              </option>
            </select>
          </div>
        </div>
        <SearchComponent v-model="searchQuery" content="reviews" />
        <div class="button-filter">
          <button class="btn-filter" @click="filterReviews">Filter</button>
          <button class="btn-clear" @click="clearFilters">Clear</button>
        </div>
      </div>
    </div>

    <div class="table-component">
      <TableComponent
        :headers="trashedHeaders"
        :items="trashedReviews"
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
        <p>Do you want to restore these selected reviews?</p>
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
import reviewService from "@/services/reviewService";
import TableComponent from "@/components/TableComponent.vue";
import PaginationComponent from "@/components/PaginationComponent.vue";
import SearchComponent from "@/components/SearchComponent.vue";
import { toast } from "vue3-toastify";
import type { ReviewData, ApiResponse } from "@/types/index";

const searchQuery = ref("");
const selectedRating = ref("");
const trashedReviews = ref<ReviewData[]>([]);
const currentPage = ref(1);
const totalPages = ref(1);
const error = ref<string | null>(null);
const isLoading = ref(false);
const selectedItems = ref<number[]>([]);
const showBulkPopup = ref(false);

const trashedHeaders = [
  { key: "user_name", label: "User Name" },
  { key: "book_name", label: "Book Name" },
  { key: "description", label: "Description" },
  { key: "rating", label: "Rating" },
];

const fetchTrashedReviews = async (page = 1, keyword = "", rating = "") => {
  isLoading.value = true;
  error.value = null;
  try {
    const response: ApiResponse<ReviewData[]> = await reviewService.getTrashed(
      page,
      keyword,
      rating
    );
    if (response.success) {
      trashedReviews.value = response.data;
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
  if (trashedReviews.value.length === 0) {
    selectedItems.value = [];
  }
};

onMounted(() => {
  fetchTrashedReviews();
});

const goToPage = (page: number) => {
  fetchTrashedReviews(page, searchQuery.value, selectedRating.value);
};

const prevPage = () => {
  if (currentPage.value > 1) {
    fetchTrashedReviews(
      currentPage.value - 1,
      searchQuery.value,
      selectedRating.value
    );
  }
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    fetchTrashedReviews(
      currentPage.value + 1,
      searchQuery.value,
      selectedRating.value
    );
  }
};

// Perform restores function
const performRestores = async (selectedItems: number[]) => {
  if (selectedItems.length === 0) {
    toast.error("Please select reviews to restore.");
    return;
  }
  try {
    await reviewService.restore(selectedItems);
    toast.success("Reviews restored successfully");
    fetchTrashedReviews(
      currentPage.value,
      searchQuery.value,
      selectedRating.value
    );
    selectedItems.value = []; // Clear selected items after restoration
  } catch (err: any) {
    console.error("Error performing restores", err);
    toast.error(`Error performing restores: ${err.message}`);
  }
  if (trashedReviews.value.length === 0) {
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
  selectedItems.value = [];
};

// Update selected items
const updateSelectedItems = (items: number[]) => {
  selectedItems.value = items;
};

const currentFilter = ref({
  rating: "",
  keyword: "",
});

// Filter reviews function
const filterReviews = () => {
  const newFilter = {
    rating: selectedRating.value,
    keyword: searchQuery.value,
  };

  if (JSON.stringify(newFilter) == JSON.stringify(currentFilter.value)) {
    return;
  }
  fetchTrashedReviews(1, searchQuery.value, selectedRating.value);
  currentFilter.value = newFilter;
};

// Clear filters function
const clearFilters = () => {
  if (searchQuery.value == "" && selectedRating.value == "") {
    return;
  }
  searchQuery.value = "";
  selectedRating.value = "";
  fetchTrashedReviews(1);
};
</script>

<style scoped>
.content-header {
  display: flex;
  align-items: center;
  gap: 10px;
}
</style>
