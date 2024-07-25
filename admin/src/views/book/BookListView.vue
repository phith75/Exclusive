<template>
  <section id="section-content">
    <div class="content">
      <div class="content-header">
        <h1>List Books</h1>
      </div>
    </div>
    <div class="action-filter">
      <div class="actions">
        <router-link :to="{ name: 'book.create' }">Create</router-link>
        <router-link :to="{ name: 'book.restore' }">Trashed</router-link>
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
          <button class="btn-filter" @click="filterBooks">Filter</button>
          <button class="btn-clear" @click="clearFilters">Clear</button>
        </div>
      </div>
    </div>
    <div class="table-component">
      <TableComponent
        :headers="bookHeaders"
        :items="books"
        @delete="deleteBook"
        @selectedItems="updateSelectedItems"
        :showCheckbox="true"
        :showActions="true"
        :editRouteName="'book.update'"
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
        <p>Do you want to delete these selected books?</p>
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
import bookService from "@/services/bookService";
import TableComponent from "@/components/TableComponent.vue";
import PaginationComponent from "@/components/PaginationComponent.vue";
import SearchComponent from "@/components/SearchComponent.vue";
import { toast } from "vue3-toastify";
import type {
  AuthorData,
  BookData,
  CategoryData,
  PublisherData,
} from "@/types/index";

// State variables
const searchQuery = ref("");
const books = ref<BookData[]>([]);
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
const bookHeaders = [
  { key: "thumbnail", label: "Thumbnail" },
  { key: "name", label: "Name" },
  { key: "category_name", label: "Category" },
  { key: "author_name", label: "Author" },
  { key: "publisher_name", label: "Publisher" },
  { key: "quantity", label: "Quantity" },
  { key: "short_description", label: "Short Description" },
  { key: "borrowed_count", label: "Borroweds" },
  { key: "wishlists_count", label: "Wishlists" },
  { key: "reviews_count", label: "Reviews" },
];

// Fetch books function
const fetchBooks = async (
  page = 1,
  keyword = "",
  category = "",
  author = "",
  publisher = ""
) => {
  isLoading.value = true;
  error.value = null;
  try {
    const response = await bookService.getBooks(
      page,
      keyword,
      category,
      author,
      publisher
    );
    if (response.success === 1) {
      books.value = response.data.map((book: BookData) => ({
        ...book,
        wishlists_count: book.wishlists.length,
        reviews_count: book.reviews.length,
      }));
      currentPage.value = response.pagination?.current_page ?? 1;
      totalPages.value = response.pagination?.last_page ?? 1;
    } else {
      throw new Error(response.message || "Failed to fetch books");
    }
  } catch (err: any) {
    error.value = err.message;
    toast.error(`Error: ${err.message}`);
  } finally {
    isLoading.value = false;
  }
};

// Fetch categories, authors, and publishers
const fetchFilters = async () => {
  try {
    const [categoriesRes, authorsRes, publishersRes] = await Promise.all([
      bookService.getCategories(),
      bookService.getAuthors(),
      bookService.getPublishers(),
    ]);

    if (categoriesRes.success == 1) {
      categories.value = categoriesRes.data;
    }
    if (authorsRes.success == 1) {
      authors.value = authorsRes.data;
    }
    if (publishersRes.success == 1) {
      publishers.value = publishersRes.data;
    }
  } catch (err: any) {
    toast.error(`Error fetching filters: ${err.message}`);
  }
};

onMounted(() => {
  fetchBooks();
  fetchFilters();
});

const currentFilter = ref({
  category: "",
  author: "",
  publisher: "",
  keyword: "",
});
const goToPage = (page: number) => {
  fetchBooks(
    page,
    searchQuery.value,
    currentFilter.value.category,
    currentFilter.value.author,
    currentFilter.value.publisher
  );
};

const prevPage = () => {
  if (currentPage.value > 1) {
    fetchBooks(
      currentPage.value - 1,
      searchQuery.value,
      currentFilter.value.category,
      currentFilter.value.author,
      currentFilter.value.publisher
    );
  }
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    fetchBooks(
      currentPage.value + 1,
      searchQuery.value,
      currentFilter.value.category,
      currentFilter.value.author,
      currentFilter.value.publisher
    );
  }
};

// Delete book function
const deleteBook = async (id: number) => {
  try {
    await bookService.deleteBook(id);
    toast.success("Book deleted successfully");
    fetchBooks(currentPage.value, searchQuery.value);
  } catch (err: any) {
    if (err.response && err.response.data.errors.error_message) {
      toast.error(err.response.data.errors.error_message);
    } else {
      toast.error(`Delete failed: ${err.message}`);
    }
  }
};

// Delete multiple books function
const deleteManyBooks = async (selectedItems: number[]) => {
  try {
    await bookService.deleteManyBooks(selectedItems);
    toast.success("Books deleted successfully");
    fetchBooks(currentPage.value, searchQuery.value);
    selectedItems.length = 0;
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
  await deleteManyBooks(selectedItems.value);
  closeBulkPopup();
};

// Update selected items
const updateSelectedItems = (items: number[]) => {
  selectedItems.value = items;
};

// Filter books function
const filterBooks = () => {
  const newFilter = {
    category: selectedCategory.value,
    author: selectedAuthor.value,
    publisher: selectedPublisher.value,
    keyword: searchQuery.value,
  };
  if (JSON.stringify(newFilter) == JSON.stringify(currentFilter.value)) {
    return;
  }

  fetchBooks(
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

  fetchBooks(1, "", "", "", "");
};
</script>

<style scoped lang="scss">
.content-header {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}
</style>
