<template>
  <div class="flash-sales">
    <div class="flash-sales-top">
      <TitleCollection :title="title.title" :subtitle="title.subtitle" />
      <div v-if="CollectionType === 'arrows'" class="carousel-arrows">
        <el-button @click="prevSlide" circle>
          <img :src="leftArrow" alt="Previous" />
        </el-button>
        <el-button @click="nextSlide" circle>
          <img :src="rightArrow" alt="Next" />
        </el-button>
      </div>
      <div v-else-if="CollectionType === 'button'" class="view-all-button">
        <el-button size="large" @click="navigateToBooks">View All</el-button>
      </div>
    </div>
    <div class="carousel-wrapper">
      <el-carousel
        v-if="displayedBooks.length > 0"
        ref="bookCarousel"
        :autoplay="true"
        :interval="5000"
        :height="heightPx"
        arrow="never"
        indicator-position="none"
      >
        <el-carousel-item v-for="(group, index) in displayedBooks" :key="index">
          <div
            class="book-row"
            v-for="(row, rowIndex) in group"
            :key="rowIndex"
          >
            <div class="book-group">
              <el-card
                v-for="(item, subIndex) in row"
                :key="subIndex"
                class="book-card"
                :body-style="{ padding: '0px' }"
                shadow="hover"
              >
                <div class="image-container" v-loading="loadingIcons[item.id]">
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
                  <el-badge
                    v-if="item.quantity_left <= 0"
                    value="Out of Stock"
                    class="out-of-stock-badge"
                  />
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
                <div class="book-info" @click="goToBookDetail(item.id)">
                  <span class="book-name">{{ item.name }}</span>
                  <p class="short-description">{{ item.short_description }}</p>
                  <div class="book-price"></div>
                  <div class="book-rating">
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
            </div>
          </div>
        </el-carousel-item>
      </el-carousel>
      <el-empty v-else></el-empty>
    </div>
    <div v-if="CollectionType === 'arrows'" class="view-all-books">
      <el-button type="primary" size="large" @click="navigateToBooks">
        View All Books
      </el-button>
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
import { ref, watch, onMounted, computed } from "vue";
import { useRouter } from "vue-router";
import {
  leftArrow,
  rightArrow,
  QuickView,
  Wishlist,
  TrashIcon,
} from "@/assets";
import { Picture as IconPicture } from "@element-plus/icons-vue";
import TitleCollection from "@/components/TitleCollection.vue";
import type { Book, Title } from "@/types";
import wishlistService from "@/services/wishlistService";
import { useCartStore } from "@/stores/cartStore";
import { useAuthStore } from "@/stores/authStore";

const props = defineProps<{
  titleCollection: Title[];
  initialViewMode: string;
  typeCollection: string;
  initialBooks: Book[];
}>();

const isDialogVisible = ref(false);
const selectedBookImage = ref("");

const openQuickView = (item: any) => {
  selectedBookImage.value = getImageUrl(item.thumbnail);
  isDialogVisible.value = true;
};

const getImageUrl = (imagePath: string) => {
  if (imagePath.startsWith("https")) {
    return imagePath;
  } else {
    return `${import.meta.env.VITE_IMAGE_URL}${imagePath}`;
  }
};

const viewMode = ref(props.initialViewMode);
const books = ref<Book[]>([...props.initialBooks]);
const displayedBooks = ref<Book[][][]>([]);
const title = ref<Title>({ ...props.titleCollection[0] });
const CollectionType = ref(props.typeCollection);
const loadingIcons = ref<{ [key: number]: boolean }>({});

const cartStore = useCartStore();
const authStore = useAuthStore();
const router = useRouter();

watch(
  () => props.initialViewMode,
  (newValue) => {
    viewMode.value = newValue;
    updateDisplayedBooks();
  }
);

watch(
  () => props.initialBooks,
  (newValue) => {
    books.value = [...newValue];
    updateDisplayedBooks();
  }
);

const itemsPerGroup = ref(4);
const heightPx = ref("410px");

const updateDisplayedBooks = () => {
  if (viewMode.value === "two-rows") {
    if (window.innerWidth <= 480) {
      itemsPerGroup.value = 2;
      heightPx.value = "820px";
    } else if (window.innerWidth <= 768) {
      itemsPerGroup.value = 3;
      heightPx.value = "780px";
    } else {
      itemsPerGroup.value = 8;
      heightPx.value = "840px";
    }
  } else {
    if (window.innerWidth <= 480) {
      itemsPerGroup.value = 1;
      heightPx.value = "410px";
    } else if (window.innerWidth <= 768) {
      itemsPerGroup.value = 3;
      heightPx.value = "480px";
    } else {
      itemsPerGroup.value = 4;
      heightPx.value = "410px";
    }
  }

  const groups = [];
  for (let i = 0; i < books.value.length; i += itemsPerGroup.value) {
    const group = books.value.slice(i, i + itemsPerGroup.value);
    const rows = [];
    for (let j = 0; j < group.length; j += 4) {
      rows.push(group.slice(j, j + 4));
    }
    groups.push(rows);
  }
  displayedBooks.value = groups;
};

onMounted(() => {
  updateDisplayedBooks();
  window.addEventListener("resize", updateDisplayedBooks);
  watch(books, () => {
    updateDisplayedBooks();
  });
});

const bookCarousel = ref();

const prevSlide = () => {
  const lastBook = books.value.pop();
  if (lastBook) {
    books.value.unshift(lastBook);
  }
  updateDisplayedBooks();
};

const nextSlide = () => {
  const firstBook = books.value.shift();
  if (firstBook) {
    books.value.push(firstBook);
  }
  updateDisplayedBooks();
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
    fetchWishlistData();
  } catch (error) {
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

const goToBookDetail = (bookId: number) => {
  router.push({ name: "BookDetail", params: { id: bookId } });
};

const navigateToBooks = () => {
  router.push({ name: "Books" });
};
const resizeByWidth = computed(() => {
  if (window.innerWidth < 768) {
    return "95%";
  }
  return "24.15%";
});

const fetchWishlistData = async () => {
  try {
    const response = await wishlistService.getWishlists();
    if (response.success == 1) {
      authStore.wishlistUser = response.data;
    }
  } catch (error) {
    console.error(error);
  }
};

onMounted(fetchWishlistData);
</script>

<style lang="scss" scoped>
.flash-sales-top{
  display: flex;
  justify-content: end;
}
.image-preview {
  width: 430px;
  @media (max-width: 768px) {
    width: 370px;
  }
  height: auto;
}
.flash-sales {
  margin-top: 40px;

  &-top {
    display: flex;
    margin-bottom: 60px;
    justify-content: space-between;
    align-items: self-end;

    .carousel-arrows {
      display: flex;
      margin: 5px 0;

      .el-button {
        margin: 0 5px;
        padding: 23px;
        background-color: rgba(245, 245, 245, 1);
      }
    }
  }

  .carousel-wrapper {
    position: relative;
    height: auto;
  }
  .el-carousel__container {
    height: fit-content;
  }

  .book-row {
    display: flex;
    justify-content: space-between;
    gap: 30px;
    margin-bottom: 30px;
    @media (max-width: 768px) {
      flex-wrap: wrap;
    }
  }

  .book-group {
    display: flex;
    justify-content: space-between;
    gap: 30px;
    @media (max-width: 768px) {
      flex-wrap: wrap;
    }
  }

  .book-card {
    flex: 1;
    width: 270px;
    border-radius: 4px;
    min-height: 375px;
    overflow: hidden;
    cursor: pointer;
    position: relative;
    @media (max-width: 768px) {
      width: calc(40% - 15px);
    }
    @media (max-width: 480px) {
      align-items: center;
      max-width: 330px;
      width: 100%;
    }

    .image-container {
      border-radius: 4px;
      border: none;
      background-color: rgba(245, 245, 245, 1);
      position: relative;
      overflow: hidden;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 250px;

      .el-image {
        object-fit: contain;
        width: 100%;
        height: 100%;
        .image-slot {
          display: flex;
          justify-content: center;
          align-items: center;
          width: 100%;
          height: 100%;
        }
        margin: 10% auto;
        display: block;
      }

      .out-of-stock-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        .el-badge__content {
          background-color: red;
          color: white;
          padding: 4px 8px;
          border-radius: 4px;
          font-weight: bold;
        }
      }

      .icon-book {
        position: absolute;
        top: 12px;
        right: 12px;
        width: 24px;
        height: 24px;
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
    }

    &:hover .add-to-cart-button {
      opacity: 1;
    }
  }

  .book-info {
    padding: 0px 14px;

    .book-name {
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
      -webkit-box-orient: vertical;
      -webkit-line-clamp: 2;
      overflow: hidden;
      font-size: 13px;
      font-weight: 400;
      margin: 5px 0;
    }

    .book-price {
      display: flex;
      align-items: center;
      margin-bottom: 5px;

      .current-price {
        font-size: 18px;
        font-weight: bold;
        color: rgba(255, 68, 0, 0.78);
        margin-right: 10px;
      }

      .original-price {
        font-size: 14px;
        color: #888;
        text-decoration: line-through;
      }
    }

    .book-rating {
      display: flex;
      position: absolute;
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

    .el-button {
      width: 100%;
      background-color: rgba(255, 69, 0, 0.78);
      border-color: rgba(255, 69, 0, 0.78);
      color: white;
      font-weight: bold;

      &:hover {
        background-color: rgba(255, 69, 0, 0.78);
        border-color: rgba(255, 69, 0, 0.78);
        color: white;
      }
    }
  }
}

.view-all-button,
.view-all-books {
  .el-button {
    background-color: rgba(255, 69, 0, 0.78);
    border-color: rgba(255, 69, 0, 0.78);
    color: white;
    font-weight: bold;
    padding: 0px 48px;
    height: 56px;
    border-radius: 4px;
    &:hover {
      background-color: rgba(255, 69, 0, 0.78);
      border-color: rgba(255, 69, 0, 0.78);
      color: white;
    }
  }
}

.example-showcase .el-loading-mask {
  z-index: 9;
  width: 5px;
}

.view-all-books {
  display: flex;
  justify-content: center;
  margin-top: 60px;

  .el-button {
    background-color: rgba(255, 69, 0, 0.78);
    border-color: rgba(255, 69, 0, 0.78);
    color: white;
    font-weight: bold;
    padding: 16px 48px;
    border-radius: 4px;
    &:hover {
      background-color: rgba(255, 69, 0, 0.78);
      border-color: rgba(255, 69, 0, 0.78);
      color: white;
    }
  }
}

@media (max-width: 768px) {
  .book-row {
    flex-wrap: wrap;
    gap: 15px;
  }

  .book-group {
    flex-wrap: wrap;
    justify-content: center;
    align-items: center;
    gap: 15px;
  }
}

@media (max-width: 480px) {
  .book-row {
    flex-direction: column;
    gap: 15px;
  }

  .book-group {
    flex-direction: column;
    gap: 15px;
  }

  .book-card {
    width: 100%;
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
