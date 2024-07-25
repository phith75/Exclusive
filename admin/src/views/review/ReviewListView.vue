<template>
  <section id="section-content">
    <div class="content">
      <div class="content-header">
        <h1>List Reviews</h1>
      </div>
    </div>

    <div class="action-filter">
      <div class="actions">
        <router-link :to="{ name: 'review.trashed' }">Trashed</router-link>
        <button
          v-if="selectedItems.length > 0"
          @click="openBulkDeletePopup"
          class="btn-delete-many"
        >
          Delete Many
        </button>
      </div>
      <div class="filters">
        <div class="filter-left">
          <div class="group-select">
            <label for="">Select Status</label>
          <select v-model="selectedStatus">
            <option value="">All Statuses</option>
            <option
              v-for="status in statusOptions"
              :key="status.value"
              :value="status.value"
            >
              {{ status.label }}
            </option>
          </select>
          </div>
          <div class="group-select">
            <label for="">Select Rating</label>
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
        :headers="reviewHeaders"
        :items="reviews"
        :statusOptions="statusOptions"
        @changeStatus="changeStatus"
        :editRouter="false"
        @selectedItems="updateSelectedItems"
        @delete="deleteReview"
        :showCheckbox="true"
        :showActions="true"
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
        <p>Do you want to delete these selected reviews?</p>
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
import reviewService from "@/services/reviewService";
import TableComponent from "@/components/TableComponent.vue";
import PaginationComponent from "@/components/PaginationComponent.vue";
import SearchComponent from "@/components/SearchComponent.vue";
import { toast } from "vue3-toastify";
import type { ReviewData } from "@/types/index";
import {
  STATUS_REVIEW_OPTIONS,
  convertEnumToOptions,
} from "@/enum/Constants.ts";

// State variables
const searchQuery = ref("");
const reviews = ref<ReviewData[]>([]);
const currentPage = ref(1);
const totalPages = ref(1);
const error = ref<string | null>(null);
const isLoading = ref(false);
const selectedItems = ref<number[]>([]);
const showBulkPopup = ref(false);

// New filters
const selectedStatus = ref("");
const selectedRating = ref("");

const reviewHeaders = [
  { key: "user_name", label: "User Name" },
  { key: "book_name", label: "Book Name" },
  { key: "description", label: "Description" },
  { key: "rating", label: "Rating" },
  { key: "status", label: "Status" },
];

const statusOptions = convertEnumToOptions(STATUS_REVIEW_OPTIONS);

const fetchReviews = async (
  page = 1,
  keyword = "",
  status = "",
  rating = ""
) => {
  isLoading.value = true;
  error.value = null;
  try {
    const response = await reviewService.getReviews(
      page,
      keyword,
      status,
      rating
    );

    if (response.success === 1) {
      reviews.value = response.data;
      currentPage.value = response.pagination?.current_page ?? 1;
      totalPages.value = response.pagination?.last_page ?? 1;
    } else {
      throw new Error(response.message || "Failed to fetch reviews");
    }
  } catch (err: any) {
    error.value = err.message;
    toast.error(`Error: ${err.message}`);
  } finally {
    isLoading.value = false;
  }
};
const currentFilter = ref({
  status: "",
  rating: "",
  keyword: "",
});

// Filter reviews function
const filterReviews = () => {
  const newFilter = {
    status: selectedStatus.value,
    rating: selectedRating.value,
    keyword: searchQuery.value,
  };

  if (JSON.stringify(newFilter) == JSON.stringify(currentFilter.value)) {
    return;
  }
  fetchReviews(
    1,
    searchQuery.value,
    selectedStatus.value,
    selectedRating.value
  );
  currentFilter.value = newFilter;
};

// Clear filters function
const clearFilters = () => {
  if (
    searchQuery.value == "" &&
    selectedStatus.value == "" &&
    selectedRating.value == ""
  ) {
    return;
  }
  searchQuery.value = "";
  selectedStatus.value = "";
  selectedRating.value = "";
  fetchReviews(1);
};

// Change review status function
const changeStatus = async (id: number, newStatus: number) => {
  try {
    const response = await reviewService.updateStatusReview(id, {
      status: newStatus,
    });
    if (response.success === 1) {
      toast.success("Review status updated successfully");
      fetchReviews(
        currentPage.value,
        searchQuery.value,
        selectedStatus.value,
        selectedRating.value
      );
    } else {
      throw new Error(response.message || "Failed to update review status");
    }
  } catch (err: any) {
    toast.error(`Error: ${err.message}`);
  }
};

// Delete review function
const deleteReview = async (id: number) => {
  try {
    const response = await reviewService.deleteReview(id);
    if (response.success === 1) {
      toast.success("Review deleted successfully");
      fetchReviews(currentPage.value, searchQuery.value);
    } else {
      throw new Error(response.message || "Failed to delete review");
    }
  } catch (err: any) {
    toast.error(`Error: ${err.message}`);
  }
};

// Perform bulk delete function
const performDeletes = async (selectedItems: number[]) => {
  if (selectedItems.length === 0) {
    toast.error("Please select reviews to delete.");
    return;
  }
  try {
    const response = await reviewService.deleteManyReviews(selectedItems);
    if (response.success === 1) {
      toast.success("Reviews deleted successfully");
      fetchReviews(currentPage.value, searchQuery.value);
    } else {
      throw new Error(response.message || "Failed to delete reviews");
    }
  } catch (err: any) {
    toast.error(`Error: ${err.message}`);
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
  await performDeletes(selectedItems.value);
  closeBulkPopup();
  selectedItems.value = [];
};

// Update selected items
const updateSelectedItems = (items: number[]) => {
  selectedItems.value = items;
};

onMounted(() => {
  fetchReviews();
});

const goToPage = (page: number) => {
  fetchReviews(
    page,
    searchQuery.value,
    selectedStatus.value,
    selectedRating.value
  );
};

const prevPage = () => {
  if (currentPage.value > 1) {
    fetchReviews(
      currentPage.value - 1,
      searchQuery.value,
      selectedStatus.value,
      selectedRating.value
    );
  }
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    fetchReviews(
      currentPage.value + 1,
      searchQuery.value,
      selectedStatus.value,
      selectedRating.value
    );
  }
};
</script>

<style scoped>
.content-header {
  display: flex;
  align-items: center;
  gap: 10px;
}
</style>
