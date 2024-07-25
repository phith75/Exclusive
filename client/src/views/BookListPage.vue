<template>
  <div class="book-list-page">
    <el-breadcrumb separator="/">
      <el-breadcrumb-item :to="{ name: 'Homepage' }">Home</el-breadcrumb-item>
      <el-breadcrumb-item>Books</el-breadcrumb-item>
    </el-breadcrumb>
    <div class="product-list">
      <div class="product-list-header">
        <TitleCollection :title="title.title" :subtitle="title.subtitle" />
      </div>
      <div class="product-list-body">
        <el-row class="header-content" :gutter="20">
          <el-col :xs="24" :sm="24" :md="6" :lg="6" :xl="6">
            <el-form label-position="top">
              <div class="filter-container">
                <el-row :gutter="20" class="filter-group">
                  <el-col :span="24">
                    <el-form-item label="Select Category">
                      <el-select
                        v-model="filters.categories_id"
                        placeholder="Select Category"
                        filterable
                        clearable
                        class="filter-select"
                      >
                        <el-option
                          v-for="category in categoriesCollection"
                          :key="category.id"
                          :label="category.name"
                          :value="category.id"
                        />
                      </el-select>
                    </el-form-item>
                  </el-col>
                  <el-col :span="24">
                    <el-form-item label="Select Author">
                      <el-select
                        v-model="filters.authors_id"
                        placeholder="Select Author"
                        filterable
                        clearable
                        class="filter-select"
                      >
                        <el-option
                          v-for="author in authorsCollection"
                          :key="author.id"
                          :label="author.name"
                          :value="author.id"
                        />
                      </el-select>
                    </el-form-item>
                  </el-col>
                  <el-col :span="24">
                    <el-form-item label="Select Publisher">
                      <el-select
                        label="Select Publisher"
                        v-model="filters.publishers_id"
                        placeholder="Select Publisher"
                        filterable
                        clearable
                        class="filter-select"
                      >
                        <el-option
                          v-for="publisher in publishersCollection"
                          :key="publisher.id"
                          :label="publisher.name"
                          :value="publisher.id"
                        />
                      </el-select>
                    </el-form-item>
                  </el-col>
                </el-row>
                <el-row class="group-filter-button" :gutter="20">
                  <el-button type="danger" @click="clearFilters"
                    >Clear Filters</el-button
                  >
                  <el-button type="primary" @click="submitFilter"
                    >Filter</el-button
                  >
                </el-row>
              </div>
            </el-form>
          </el-col>
          <el-col :xs="24" :sm="24" :md="18" :lg="18" :xl="18">
            <div v-if="loadingMenuData">
              <SkeletonBookList />
            </div>
            <div v-else-if="!loadingMenuData && books.length === 0">
              <el-empty description="No books available" />
            </div>
            <div v-else>
              <el-row :gutter="20">
                <el-col
                  v-for="(item, itemIndex) in books"
                  :key="itemIndex"
                  :xs="24"
                  :sm="12"
                  :md="8"
                  :lg="8"
                  :xl="8"
                >
                  <el-card
                    class="product-card"
                    :body-style="{ padding: '0px' }"
                    shadow="hover"
                  >
                    <div
                      class="image-container"
                      v-loading="loadingIcons[item.id]"
                    >
                      <el-image
                        :src="getImageUrl(item.thumbnail)"
                        class="image"
                        :alt="item.name"
                      >
                        <template #error>
                          <div class="image-slot">
                            <el-icon><icon-picture /></el-icon>
                          </div>
                        </template>
                      </el-image>
                      <el-button
                        class="add-to-cart-button"
                        size="default"
                        @click="addToCart(item)"
                      >
                        Add to Cart
                      </el-button>
                      <div class="icon-book">
                        <img
                          :src="isInWishlist(item.id) ? TrashIcon : Wishlist"
                          class="wishlist-icon"
                          alt="Wishlist"
                          width="26px"
                          @click="toggleWishlist(item.id)"
                        />
                        <img
                          :src="QuickView"
                          class="quickview-icon"
                          alt="Quick View"
                          @click="openQuickView(item)"
                        />
                      </div>
                    </div>
                    <div
                      class="product-info"
                      @click="goToProductDetail(item.id)"
                    >
                      <span class="product-name">{{ item.name }}</span>
                      <p class="short-description">
                        {{ item.short_description }}
                      </p>
                      <div class="product-rating">
                        <el-rate
                          v-model="item.avg_rating"
                          disabled
                          show-score
                          text-color="#ff9900"
                        />
                        <span>({{ item.rating_count }})</span>
                      </div>
                    </div>
                  </el-card>
                </el-col>
              </el-row>
            </div>
            <el-pagination
              :hide-on-single-page="true"
              class="pagination"
              v-if="totalPages > 1"
              :current-page="currentPage"
              :page-size="booksPerPage"
              :total="totalPages * booksPerPage"
              layout="prev, pager, next"
              @current-change="goToPage"
            />
          </el-col>
        </el-row>
      </div>
    </div>
  </div>
  <el-dialog
    v-model="isDialogVisible"
    title="Quick View"
    :width="resizeByWidth"
  >
    <el-image
      :src="selectedBookImage"
      fit="contain"
      class="image-preview"
    ></el-image>
  </el-dialog>
</template>

<script lang="ts" setup>
import { ref, onMounted, watch, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import { Picture as IconPicture } from "@element-plus/icons-vue";
import { QuickView, Wishlist, TrashIcon } from "@/assets";
import TitleCollection from "@/components/TitleCollection.vue";
import type { Book, Title, Category, Author, Publisher } from "@/types";
import bookService from "@/services/bookService";
import categoryService from "@/services/categoryService";
import authorService from "@/services/authorService";
import publisherService from "@/services/publisherService";
import { useCartStore } from "@/stores/cartStore";
import { useAuthStore } from "@/stores/authStore";
import SkeletonBookList from "@/components/SkeletonBookList.vue";

const titleCollection = ref<Title[]>([
  {
    title: "List Books",
    subtitle: "Check out books in our library",
  },
]);

const books = ref<Book[]>([]);
const categoriesCollection = ref<Category[]>([]);
const authorsCollection = ref<Author[]>([]);
const publishersCollection = ref<Publisher[]>([]);
const loadingMenuData = ref(true);
const loadingIcons = ref<{ [key: number]: boolean }>({});

const title = ref<Title>({ ...titleCollection.value[0] });

const cartStore = useCartStore();
const authStore = useAuthStore();
const router = useRouter();
const route = useRoute();

const searchQuery = ref("");
const currentPage = ref(1);
const totalPages = ref(1);
const booksPerPage = 12;

const isDialogVisible = ref(false);
const selectedBookImage = ref("");

const openQuickView = (item) => {
  selectedBookImage.value = getImageUrl(item.thumbnail);
  isDialogVisible.value = true;
};
const filters = ref<{
  keywords: string;
  authors_id: string;
  publishers_id: string;
  categories_id: string;
}>({
  keywords: "",
  authors_id: "",
  publishers_id: "",
  categories_id: "",
});

const resizeByWidth = computed(() => {
  if (window.innerWidth < 768) {
    return "95%";
  }
  return "24.15%";
});

watch(
  () => route.query,
  async (newQuery) => {
    filters.value.keywords = newQuery.keywords
      ? (newQuery.keywords as string)
      : "";

    await fetchBooks(1, filters.value.keywords);
  }
);

const getImageUrl = (imagePath: string) => {
  if (imagePath.startsWith("https")) {
    return imagePath;
  } else {
    return `${import.meta.env.VITE_IMAGE_URL}${imagePath}`;
  }
};

const isInWishlist = (bookId: number) => {
  return authStore.wishlistUser.some(
    (wishlistItem) => wishlistItem.book_id === bookId
  );
};

const toggleWishlist = async (bookId: number) => {
  try {
    loadingIcons.value = { ...loadingIcons.value, [bookId]: true };
    await authStore.addToWishlist(bookId);
  } catch (error) {
    console.error(error);
  } finally {
    loadingIcons.value = { ...loadingIcons.value, [bookId]: false };
  }
};

const addToCart = (item: Book) => {
  cartStore.addToCart(
    item.id,
    1,
    item.quantity_left,
    item.name,
    item.thumbnail
  );
};

const goToProductDetail = (bookId: number) => {
  router.push({ name: "BookDetail", params: { id: bookId } });
};

const fetchBooks = async (page = 1, keyword = "") => {
  try {
    const response = await bookService.getBooks(
      page,
      keyword,
      filters.value.authors_id,
      filters.value.publishers_id,
      filters.value.categories_id
    );

    if (response.success == 1) {
      books.value = response.data;
      currentPage.value = response.pagination?.current_page ?? 1;
      totalPages.value = response.pagination?.last_page ?? 1;
    } else {
      throw new Error(response.message || "Failed to fetch books");
    }
  } catch (error) {
    console.error(error);
  }
};

const fetchCategories = async () => {
  try {
    const response = await categoryService.getCategories();
    if (response.success === 1) {
      categoriesCollection.value = response.data;
    } else {
      throw new Error(response.message || "Failed to fetch categories");
    }
  } catch (error) {
    console.error(error);
  }
};

const fetchAuthors = async () => {
  try {
    const response = await authorService.getAuthors();
    if (response.success === 1) {
      authorsCollection.value = response.data;
    } else {
      throw new Error(response.message || "Failed to fetch authors");
    }
  } catch (error) {
    console.error(error);
  }
};

const fetchPublishers = async () => {
  try {
    const response = await publisherService.getPublishers();
    if (response.success === 1) {
      publishersCollection.value = response.data;
    } else {
      throw new Error(response.message || "Failed to fetch publishers");
    }
  } catch (error) {
    console.error(error);
  }
};

const clearFilters = () => {
  filters.value = {
    keywords: "",
    authors_id: "",
    publishers_id: "",
    categories_id: "",
  };
  submitFilter();
};

const currentFilter = ref({
  author: "",
  publisher: "",
  category: "",
  keyword: "",
});

const submitFilter = () => {
  const newFilter = {
    author: filters.value.authors_id,
    publisher: filters.value.publishers_id,
    category: filters.value.categories_id,
    keyword: filters.value.keywords,
  };

  if (
    newFilter.keyword == currentFilter.value.keyword &&
    newFilter.author == currentFilter.value.author &&
    newFilter.publisher == currentFilter.value.publisher &&
    newFilter.category == currentFilter.value.category
  ) {
    return;
  }

  currentFilter.value = newFilter;
  fetchBooks(1, newFilter.keyword);
};

const goToPage = (page: number) => {
  currentPage.value = page;
  fetchBooks(page);
};

onMounted(async () => {
  loadingMenuData.value = true;

  await Promise.all([fetchCategories(), fetchAuthors(), fetchPublishers()]);

  await fetchBooks();

  loadingMenuData.value = false;
});
</script>
<style scoped lang="scss">
.image-preview {
  width: 430px;
  @media (max-width: 768px) {
    width: 370px;
  }
  height: auto;
}
.filter-container {
  .filter-group {
    display: flex;
    flex-direction: column;
    :deep(.el-select__wrapper) {
      height: 50px;
    }
  }
  .group-filter-button {
    display: flex;
    padding: 0px 10px;
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
    .el-button {
      width: 40%;
    }
  }
  .el-button {
    height: 50px;
    margin-top: 20px;
  }
}
.book-list-page {
  margin: 40px auto 0 auto;
  width: 100%;
  max-width: 1170px;

  .product-list {
    margin-top: 2rem;
    &-header {
      margin-bottom: 40px;
    }

    &-body {
      display: flex;
      flex-direction: column;
      gap: 30px;

      .product-card {
        margin: 0px auto 24px auto;
        border-radius: 4px;
        min-height: 375px;
        overflow: hidden;

        position: relative;
        cursor: pointer;

        .image-container {
          border-radius: 4px;
          background-color: rgba(245, 245, 245, 1);
          position: relative;
          overflow: hidden;
          display: flex;
          justify-content: center;
          align-items: center;
          height: 250px;

          .el-image {
            margin: 10% auto;
            display: block;
            width: 100%;
            height: 100%;
          }

          .icon-book {
            position: absolute;
            top: 12px;
            right: 12px;
            display: flex;
            gap: 6px;
            flex-direction: column;
            align-items: center;
            cursor: pointer;
            .wishlist-icon,
            .quickview-icon {
              border-radius: 50%;
              background-color: rgba(255, 255, 255, 1);
            }
          }
        }

        .add-to-cart-button {
          position: absolute;
          color: rgba(255, 255, 255, 1);
          bottom: 0;
          left: 0;
          height: 41px;
          border-radius: 0 0 4px 4px;
          width: 100%;
          opacity: 0;
          background-color: rgba(255, 69, 0, 0.78);
          transition: opacity 0.3s ease;
        }

        .product-info {
          padding: 0px 14px;
          display: flex;
          flex-direction: column;
          .product-name {
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 1;
            overflow: hidden;
            font-size: 16px;
            font-weight: bold;
            margin: 5px 0;
          }

          .short-description {
            display: -webkit-box;
            flex: 1;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            overflow: hidden;
            font-size: 13px;
            font-weight: 400;
            margin: 5px 0;
          }

          .product-rating {
            position: absolute;
            display: flex;
            align-items: center;
            bottom: 5px;
            .el-rate {
              margin-right: 5px;
            }
            span {
              font-size: 14px;
              color: #888;
            }
          }
        }
        &:hover .add-to-cart-button {
          opacity: 1;
        }
      }
    }
  }
  .pagination {
    display: flex;
    justify-content: center;
  }
}

@media (max-width: 1200px) {
  .product-list-body {
    .header-content {
      gap: 20px;
    }
    .product-card {
      flex: 1 1 calc(50% - 20px);
      width: calc(50% - 20px);
      max-width: 100%;
    }
  }
}

@media (max-width: 768px) {
  .product-list-body {
    .product-card {
      flex: 1 1 100%;
      justify-content: center;
      width: 80%;
    }
  }
}
.el-icon {
  font-size: 45px;
  cursor: pointer;
  color: rgba(255, 69, 0, 0.78);
  .icon-picture {
    font-size: 100px;
    color: rgba(255, 69, 0, 0.78);
  }
}
</style>
