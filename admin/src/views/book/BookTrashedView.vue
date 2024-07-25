<template>
  <section id="section-content">
    <div class="content">
      <div class="content-header">
        <h1>List Restores Books</h1>
      </div>
    </div>
    <div class="action-filter">
      <div class="actions">
        <router-link :to="{ name: 'book.list' }">Return to List</router-link>
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
            <label for="">Select Category</label>
            <select v-model="selectedCategory">
              <option value="">All Categories</option>
              <option
                v-for="category in categories"
                :key="category.id"
                :value="category.id"
              >
                {{ category.name }}
              </option>
            </select>
          </div>
          <div class="group-select">
            <label for="">Select Author</label>
            <select v-model="selectedAuthor">
              <option value="">All Authors</option>
              <option
                v-for="author in authors"
                :key="author.id"
                :value="author.id"
              >
                {{ author.name }}
              </option>
            </select>
          </div>
          <div class="group-select">
            <label for="">Select Publishers</label>
            <select v-model="selectedPublisher">
              <option value="">All Publishers</option>
              <option
                v-for="publisher in publishers"
                :key="publisher.id"
                :value="publisher.id"
              >
                {{ publisher.name }}
              </option>
            </select>
          </div>
        </div>
        <div class="filter-right">
          <SearchComponent v-model="searchQuery" content="books" />
        </div>
        <div class="button-filter">
          <button class="btn-filter" @click="filterRestores">Search</button>
          <button class="btn-clear" @click="clearFilters">Clear</button>
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
        <p>Do you want to restore these selected books?</p>
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
import bookService from "@/services/bookService";
import TableComponent from "@/components/TableComponent.vue";
import PaginationComponent from "@/components/PaginationComponent.vue";
import SearchComponent from "@/components/SearchComponent.vue";
import { toast } from "vue3-toastify";
import type {
  BookData,
  ApiResponse,
  CategoryData,
  AuthorData,
  PublisherData,
} from "@/types/index";

// State variables
const searchQuery = ref("");
const restores = ref<BookData[]>([]);
const categories = ref<CategoryData[]>([]);
const authors = ref<AuthorData[]>([]);
const publishers = ref<PublisherData[]>([]);
const selectedCategory = ref("");
const selectedAuthor = ref("");
const selectedPublisher = ref("");
const currentPage = ref(1);
const totalPages = ref(1);
const error = ref<string | null>(null);
const isLoading = ref(false);
const selectedItems = ref<number[]>([]);
const showBulkPopup = ref(false);

// Table headers
const restoreHeaders = [
  { key: "name", label: "Name" },
  { key: "thumbnail", label: "Thumbnail" },
];

// Fetch restores function
const fetchRestores = async (
  page = 1,
  keyword = "",
  category = "",
  author = "",
  publisher = ""
) => {
  isLoading.value = true;
  error.value = null;
  try {
    const response: ApiResponse<BookData[]> = await bookService.getTrashed(
      page,
      keyword,
      category,
      author,
      publisher
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

// Fetch filters function
const fetchFilters = async () => {
  try {
    const [categoriesRes, authorsRes, publishersRes] = await Promise.all([
      bookService.getCategories(),
      bookService.getAuthors(),
      bookService.getPublishers(),
    ]);
    if (categoriesRes.success === 1) {
      categories.value = categoriesRes.data;
    }
    if (authorsRes.success === 1) {
      authors.value = authorsRes.data;
    }
    if (publishersRes.success === 1) {
      publishers.value = publishersRes.data;
    }
  } catch (err: any) {
    toast.error(`Error fetching filters: ${err.message}`);
  }
};

// Fetch data on mount
onMounted(() => {
  fetchRestores();
  fetchFilters();
});

// Pagination functions
const goToPage = (page: number) => {
  fetchRestores(
    page,
    searchQuery.value,
    selectedCategory.value,
    selectedAuthor.value,
    selectedPublisher.value
  );
};

const prevPage = () => {
  if (currentPage.value > 1) {
    fetchRestores(
      currentPage.value - 1,
      searchQuery.value,
      selectedCategory.value,
      selectedAuthor.value,
      selectedPublisher.value
    );
  }
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    fetchRestores(
      currentPage.value + 1,
      searchQuery.value,
      selectedCategory.value,
      selectedAuthor.value,
      selectedPublisher.value
    );
  }
};

// Perform restores function
const performRestores = async (selectedItems: number[]) => {
  if (selectedItems.length === 0) {
    toast.error("Please select books to restore.");
    return;
  }
  try {
    await bookService.restore(selectedItems);
    toast.success("Books restored successfully");
    fetchRestores(
      currentPage.value,
      searchQuery.value,
      selectedCategory.value,
      selectedAuthor.value,
      selectedPublisher.value
    );
    selectedItems.length = 0; // Clear selected items after restoration
  } catch (err: any) {
    console.error("Error performing restores", err);
    toast.error(`Error performing restores: ${err.message}`);
  }
  if (restores.value.length === 0) {
    selectedItems.value = [];
  }
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
  if (restores.value.length === 0) {
    selectedItems.value = [];
  }
};

// Update selected items
const updateSelectedItems = (items: number[]) => {
  selectedItems.value = items;
};

// Handle restore from TableComponent
const handleRestore = async (id: number) => {
  await performRestores([id]);
};

// Search restores function
const currentFilter = ref({
  category: "",
  author: "",
  publisher: "",
  keyword: "",
});

// Filter restores function
const filterRestores = () => {
  const newFilter = {
    category: selectedCategory.value,
    author: selectedAuthor.value,
    publisher: selectedPublisher.value,
    keyword: searchQuery.value,
  };
  if (JSON.stringify(newFilter) == JSON.stringify(currentFilter.value)) {
    return;
  }

  fetchRestores(
    1,
    searchQuery.value,
    newFilter.category,
    newFilter.author,
    newFilter.publisher
  );
  currentFilter.value = newFilter;
};

// Clear filters function
const clearFilters = () => {
  searchQuery.value = "";
  selectedCategory.value = "";
  selectedAuthor.value = "";
  selectedPublisher.value = "";

  fetchRestores(1, "", "", "", "");
};
</script>

<style scoped>
.popup-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
}

.popup-content {
  background: #fff;
  padding: 20px;
  border-radius: 8px;
  text-align: center;
  position: relative;
}

.close {
  position: absolute;
  top: 10px;
  right: 10px;
  cursor: pointer;
  font-size: 20px;
}
</style>
