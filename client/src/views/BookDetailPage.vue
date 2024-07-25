<template>
  <div class="book-detail">
    <div class="breadcrumb">
      <el-breadcrumb separator="/">
        <el-breadcrumb-item :to="{ path: '/home' }">Home</el-breadcrumb-item>
        <el-breadcrumb-item>{{ book.name }}</el-breadcrumb-item>
      </el-breadcrumb>
    </div>
    <el-row>
      <el-col :span="16" :xs="24" class="image-gallery">
        <div class="box-thumbnail">
          <el-image
            v-for="(image, index) in book.images"
            :key="index"
            :src="getImageUrl(image)"
            :alt="`image-${index + 1}`"
            @click="selectImage(image)"
            class="thumbnail"
            :class="{ 'selected-thumbnail': image === selectedImage }"
          />
        </div>
        <div class="box-image-main">
          <el-image
            v-if="selectedImage"
            :src="getImageUrl(selectedImage)"
            class="main-image"
            :hide-on-click-modal="true"
            :preview-src-list="[getImageUrl(selectedImage)]"
          />
        </div>
      </el-col>

      <el-col :span="8" :xs="24" class="details">
        <h2>{{ book.name }}</h2>
        <div class="rating">
          <el-rate
            v-model="book.avg_rating"
            disabled
            show-score
            text-color="#ff9900"
          />
          <span>({{ book.rating_count }} Reviews)</span>
          <span
            class="stock-status"
            :class="{ 'out-of-stock': book.quantity_left <= 0 }"
          >
            {{
              book.quantity_left > 0
                ? `Quantity Left: ${book.quantity_left}`
                : "Out of Stock"
            }}
          </span>
        </div>
        <div class="short-description">
          <p>{{ book.short_description }}</p>
        </div>
        <div class="info">
          <p>
            Author: <span>{{ book.author_name }}</span>
          </p>
          <p>
            Publisher: <span>{{ book.publisher_name }}</span>
          </p>
          <p>
            Category: <span>{{ book.category_name }}</span>
          </p>
        </div>
        <div class="actions">
          <el-input-number
            v-model="quantity"
            :min="book.quantity_left > 0 ? 1 : 0"
            :max="book.quantity_left"
            :disabled="book.quantity_left === 0"
          />
          <el-button
            type="primary"
            class="submit-button"
            @click="addToCart"
            :disabled="book.quantity_left === 0"
          >
            Add to Cart
          </el-button>
          <el-button square @click="toggleWishlist" class="wishlist-button">
            <img :src="wishlistIcon" alt="Wishlist" />
          </el-button>
        </div>
        <div class="delivery-info">
          <div class="delivery-option">
            <img :src="DeliveryIcon" alt="Free Delivery" />
            <div>
              <p>Free Delivery</p>
              <a href="#">Enter your postal code for Delivery Availability</a>
            </div>
          </div>
          <div class="delivery-option">
            <img :src="ReturnIconDetail" alt="Return Delivery" />
            <div>
              <p>Return Delivery</p>
              <a href="#">Free 30 Days Delivery Returns. Details</a>
            </div>
          </div>
        </div>
      </el-col>
    </el-row>
    <div class="description">
      <TitleCollection title="Description" subtitle="" />
      <div v-html="book.description" />
    </div>
    <BookCollection
      class="additional-book-collection"
      :titleCollection="[{ title: 'Recommended', subtitle: 'For You' }]"
      :typeCollection="'button'"
      :initialViewMode="'one-rows'"
      :initialBooks="recommendedBooks"
    />
    <div class="review-section">
      <div class="statistics">
        <TitleCollection title="Rating Statistics" subtitle="" />
        <div class="rating-overview">
          <el-rate
            size="large"
            v-model="book.avg_rating"
            disabled
            text-color="#ff9900"
          />
          <span>{{ book.avg_rating }}</span>
          <span>Star</span>
        </div>
        <div class="star-stats">
          <div v-for="i in 5" :key="i" class="star-row">
            <span>{{ i }} star</span>
            <el-progress
              :text-inside="true"
              :stroke-width="24"
              :percentage="getStarPercentage(i)"
            />
          </div>
        </div>
        <TitleCollection
          title="Create Review"
          class="review-title"
          subtitle=""
        />
        <div class="create-review" v-loading="loadingReviewSubmit">
          <el-form
            ref="reviewForm"
            :model="newReview"
            require-asterisk-position="right"
            label-position="top"
          >
            <el-form-item
              label="Rate"
              prop="rating"
              :rules="[
                {
                  required: true,
                  message: 'Rating is required',
                  trigger: 'change',
                },
              ]"
            >
              <el-rate
                v-model="newReview.rating"
                show-text
                text-color="#ff9900"
              />
            </el-form-item>
            <el-form-item
              label="Description"
              prop="description"
              :rules="[
                {
                  required: true,
                  message: 'Description is required',
                  trigger: 'blur',
                },
              ]"
            >
              <el-input
                v-model="newReview.description"
                type="textarea"
                resize="vertical"
                placeholder="Enter your description"
                class="review-description"
                :autosize="{ minRows: 8, maxRows: 16 }"
              />
            </el-form-item>
            <el-button type="primary" @click="submitReview">Submit</el-button>
          </el-form>
        </div>
        <div class="circular-progress-bar"></div>
      </div>
    </div>
    <TitleCollection title="Reviews" class="review-title" subtitle="" />

    <div class="reviews">
      <div
        v-for="review in book.reviews.slice()"
        :key="review.id"
        class="review"
      >
        <p>
          <strong>{{ review.user_name }} </strong> {{ review.date }}
        </p>
        <el-rate
          v-model="review.rating"
          disabled
          show-text
          text-color="#ff9900"
        />
        <p class="review-show-description">{{ review.description }}</p>
      </div>
    </div>
    <el-pagination
      :pager-count="5"
      align="right"
      :current-page="currentPage"
      :page-size="reviewsPerPage"
      :total="totalPages * reviewsPerPage"
      layout="prev, pager, next"
      @current-change="goToPage"
    />
  </div>
</template>

<script lang="ts" setup>
import { onMounted, ref, computed, watch } from "vue";
import BookCollection from "@/components/BookCollection.vue";
import { ReturnIconDetail, TrashIcon, DeliveryIcon, Wishlist } from "@/assets";
import bookService from "@/services/bookService";
import wishlistService from "@/services/wishlistService";
import type { Book } from "@/types";
import { useRoute } from "vue-router";
import { useCartStore } from "@/stores/cartStore";
import { useAuthStore } from "@/stores/authStore";
import TitleCollection from "@/components/TitleCollection.vue";
import { toast } from "vue3-toastify";

const route = useRoute();
const authStore = useAuthStore();
const cartStore = useCartStore();
const loadingReviewSubmit = ref(false);
const currentPage = ref(1);
const totalPages = ref(1);
const reviewsPerPage = 12;
const reviewForm = ref();
const book = ref<Book>({
  id: -1,
  thumbnail: "",
  name: "",
  slug: "",
  category_id: 0,
  category_name: "",
  author_id: 0,
  author_name: "",
  publisher_id: 0,
  publisher_name: "",
  quantity: 0,
  quantity_left: 0,
  description: "",
  short_description: "",
  borrowed_count: 0,
  wishlists: [],
  reviews: [],
  avg_rating: 0,
  rating_count: 0,
  images: [],
});

const quantity = ref(1);
const bookId = computed(() => route.params.id as string);
watch(bookId, () => {
  getBookDetails(bookId.value);
});

const fetchReviews = async (page: number) => {
  try {
    const response = await bookService.getReviews(bookId.value, page);
    if (response.success == 1) {
      book.value.reviews = response.data;
      totalPages.value = response.pagination?.last_page ?? 1;
      currentPage.value = response.pagination?.current_page ?? 1;
    } else {
      throw new Error(response.message || "Failed to fetch reviews");
    }
  } catch (error) {
    console.error(error);
  }
};

const getBookDetails = async (id: string) => {
  try {
    const response = await bookService.getBookById(id);
    if (response.success == 1) {
      const bookData = response.data;
      const thumbnail = bookData.thumbnail;

      bookData.images = [
        thumbnail,
        ...bookData.images.map((image: any) =>
          typeof image === "string" ? image : image.image
        ),
      ];
      fetchReviews(1);
      book.value = {
        ...bookData,
      };

      selectedImage.value = bookData.images[0];
      quantity.value = bookData.quantity_left > 0 ? 1 : 0;
    } else {
      throw new Error(response.message || "Failed to fetch book details");
    }
  } catch (error) {
    console.error(error);
  }
};
const goToPage = (page: number) => {
  currentPage.value = page;
  fetchReviews(page);
};

const getImageUrl = (imagePath: string) => {
  if (imagePath.startsWith("https")) {
    return imagePath;
  } else {
    return `${import.meta.env.VITE_IMAGE_URL}${imagePath}`;
  }
};
const selectedImage = ref<string | null>(null);
const selectImage = (image: string) => {
  selectedImage.value = image;
};

const addToCart = () => {
  cartStore.addToCart(
    book.value.id,
    quantity.value,
    book.value.quantity_left,
    book.value.name,
    book.value.thumbnail
  );
};

const isInWishlist = computed(() => {
  return authStore.getWishlist.some(
    (wishlistItem) => wishlistItem.book_id === book.value.id
  );
});
const wishlistIcon = computed(() =>
  isInWishlist.value ? TrashIcon : Wishlist
);

const toggleWishlist = async () => {
  try {
    await authStore.addToWishlist(book.value.id);
  } catch (error) {
    console.error(error);
  }
};

const recommendedBooks = ref<Book[]>([]);

const loadRecommendedBooks = async () => {
  try {
    const response = await bookService.getRecommendedBooks();
    if (response.success === 1) {
      recommendedBooks.value = response.data;
    } else {
      throw new Error(response.message || "Failed to fetch recommended books");
    }
  } catch (error) {
    console.error(error);
  }
};

const newReview = ref({
  rating: 0,
  description: "",
  book_id: bookId.value,
});

const submitReview = async () => {
  try {
    await reviewForm.value.validate();

    loadingReviewSubmit.value = true;
    const response = await bookService.submitReview(newReview.value);
    if (response.success) {
      await getBookDetails(bookId.value);
      toast.success("Review submitted successfully");
      newReview.value = {
        rating: 0,
        description: "",
        book_id: bookId.value,
      };
    } else {
    }
  } catch (error) {
  } finally {
    loadingReviewSubmit.value = false;
  }
};

const getStarPercentage = (star: number) => {
  const totalReviews = book.value.rating_count;
  if (totalReviews == 0) return 0;
  const starCount = book.value.reviews.filter(
    (review: any) => review.rating == star
  ).length;

  const percentage = (starCount / totalReviews) * 100;
  return Number(percentage.toFixed(1));
};

onMounted(async () => {
  await getBookDetails(bookId.value);
  await loadRecommendedBooks();
  try {
    const response = await wishlistService.getWishlists();
    if (response.success == 1) {
      authStore.setWishlist(response.data);
    }
  } catch (error) {
    console.error(error);
  }
});
</script>

<style scoped lang="scss">
.book-detail {
  max-width: 1170px;
  margin: 80px auto 0 auto;

  .breadcrumb {
    margin-bottom: 20px;
  }

  .image-gallery {
    display: flex;
    gap: 30px;
    max-height: 600px;

    .box-thumbnail {
      display: flex;
      flex-direction: column;
      height: 100%;
      gap: 16px;
      overflow-y: auto;
      max-height: 600px;
      .thumbnail {
        margin-right: 5px;
        cursor: pointer;
        max-width: 170px;
        min-height: 140px;
        border: 2px solid transparent;
        transition: border-color 0.3s;
        background-color: rgba(245, 245, 245, 1);

        :deep(.el-image__inner) {
          margin: auto;
          display: flex;
          align-items: center;
          width: 75%;
          max-width: 160px;
        }
      }
      .selected-thumbnail {
        border-color: var(--primary-color);
      }
    }

    .box-image-main {
      background-color: rgba(245, 245, 245, 1);
      height: 600px;
      max-width: 500px;
      width: 100%;
      cursor: pointer;
      border: var(--primary-color);
      .el-image {
        width: 80%;
        height: 80%;
        margin: 10% auto;
        display: block;
      }
    }
  }
  .el-pagination {
    margin-top: 20px;
    justify-content: end;
  }
  .details {
    flex: 1;
    h2 {
      margin-bottom: 10px;
      display: -webkit-box;
      -webkit-box-orient: vertical;
      -webkit-line-clamp: 2;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: normal;
    }

    .rating {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 10px;

      .stock-status {
        color: rgb(13, 223, 13);
      }
      .out-of-stock {
        color: rgb(255, 0, 0);
      }
    }

    .short-description {
      border-bottom: 1px solid rgba(0, 0, 0, 0.5);
      display: -webkit-box;
      -webkit-box-orient: vertical;
      -webkit-line-clamp: 3;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: normal;
      padding-bottom: 5px;
    }

    .info {
      margin: 10px 0 20px 0;
      p {
        margin: 5px 0;
        font-weight: 600;
        span {
          font-weight: normal;
        }
      }
    }

    .actions {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 20px;
    }

    .submit-button {
      width: 165px;
      height: 40px;
    }

    .wishlist-button {
      width: 40px;
      height: 40px;
    }

    .delivery-info {
      display: flex;
      gap: 20px;
      flex-direction: column;
      margin-top: 40px;

      .delivery-option {
        display: flex;
        align-items: center;
        padding: 10px;
        border: 1px solid rgba(0, 0, 0, 0.5);
        border-radius: 5px;
        gap: 10px;
      }

      img {
        width: 30px;
        height: 30px;
      }

      p {
        margin: 0;
        font-weight: 600;
        font-size: 16px;
      }

      a {
        color: #000000;
        font-size: 12px;
        &:hover {
          text-decoration: underline;
        }
      }
    }
  }

  .description {
    margin-top: 140px;
    padding-bottom: 24px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.5);
  }

  .additional-book-collection {
    margin-top: 140px;
  }

  .review-section {
    margin-top: 40px;
    display: flex;
    flex-direction: column;
    gap: 20px;

    .rating-overview {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 24px;
    }

    .create-review {
      display: flex;
      flex-direction: column;
      border: 1px solid rgba(0, 0, 0, 0.1);
      padding: 20px;
      border-radius: 5px;
      gap: 20px;

      h3 {
        margin-bottom: 10px;
      }

      .review-description {
        width: 100%;
      }
      .el-button {
        margin-top: 10px;
        width: 100%;
        max-width: 200px;
      }
    }

    .statistics {
      display: flex;
      flex-direction: column;
      gap: 20px;

      .circular-progress-bar {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;

        svg {
          width: 100px;
          height: 100px;
        }

        span {
          font-size: 24px;
          font-weight: bold;
        }
      }

      .star-stats {
        display: flex;
        flex-direction: column;
        gap: 10px;

        .star-row {
          display: flex;
          align-items: center;
          gap: 10px;

          span {
            width: 50px;
          }

          .el-progress {
            flex: 1;
          }
        }
      }
    }
  }
  .review-title {
    margin: 80px 0 30px 0;
  }
  .reviews {
    display: flex;
    flex-direction: column;
    gap: 20px;
    overflow-y: scroll;
    max-height: 500px;
    height: 100%;

    .review {
      display: flex;
      flex-direction: column;
      border: 1px solid rgba(0, 0, 0, 0.1);
      padding: 20px;
      border-radius: 5px;

      p {
        margin: 12px 0;
        white-space: pre-wrap;
      }
    }
  }
}

@media (max-width: 768px) {
  .el-row {
    display: flex;
    gap: 20px;
    .image-gallery {
      display: flex;
      flex-direction: column;
      flex-direction: column-reverse;
      gap: 12px;
      max-height: fit-content;
      .box-image-main {
        width: 100%;
        height: 300px;
        .image-main {
          width: 100%;
        }
      }

      .box-thumbnail {
        width: 100%;
        max-height: 78px;
        flex-direction: row;
        gap: 8px;
        overflow-x: auto;
        .thumbnail {
          width: 78px;
          min-width: 78px;
          min-height: fit-content;
        }
      }
    }

    .details {
      width: 100%;
      .rating {
        flex-direction: row;
        align-items: center;
        justify-content: flex-start;
        flex-wrap: wrap;
        .rating-overview {
          gap: 4px;
          flex-direction: row;
        }
      }
      .delivery-info {
        flex-direction: column;
      }
    }
  }
}
</style>
