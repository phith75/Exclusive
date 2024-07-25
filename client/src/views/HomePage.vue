<template>
  <div class="container">
    <el-row class="header-content" :gutter="20">
      <el-col :xs="24" :sm="24" :md="6" :lg="6" :xl="6" style="border-right: 0.4px solid #FF4500C7;">
        <template v-if="loadingCategories">
          <SkeletonCategoryCard />
        </template>
        <template v-else>
          <CommonMenu
            :menuItems="menuData"
            :loading="loadingCategories"
            @filter="handleFilter"
          />
        </template>
      </el-col>

      <el-col :xs="24" :sm="24" :md="18" :lg="18" :xl="18">
        <el-carousel :interval="4000" class="carousel-wrapper">
          <el-carousel-item v-for="(item, index) in carouselItems" :key="index">
            <img :src="item.imageUrl" :alt="item.title" />
            <div class="carousel-item-content">
              <h3 class="small justify-center">{{ item.title }}</h3>
              <p>{{ item.description }}</p>
            </div>
          </el-carousel-item>
        </el-carousel>
      </el-col>
    </el-row>

    <div>
      <template v-if="loadingBooks">
        <SkeletonBookCard viewMode="one-rows" />
      </template>
      <template v-else>
        <BookCollection
          class="top-books"
          :titleCollection="mostBorrowedBook"
          :typeCollection="CollectionType"
          :initialViewMode="'one-rows'"
          :initialBooks="mostBorrowedBookCollection"
        />
      </template>
    </div>
    <template v-if="loadingCategories">
      <SkeletonCategoryCollection />
    </template>
    <template v-else>
      <CategoryCollection
        class="category-collection"
        :initCategories="categoriesCollection"
      />
    </template>
    <div>
      <template v-if="loadingBooks">
        <SkeletonBookCard viewMode="one-rows" />
      </template>
      <template v-else>
        <BookCollection
          typeCollection="button"
          :initialBooks="BookCollectionData"
          :titleCollection="topBook"
          :initialViewMode="'one-rows'"
        />
      </template>
    </div>

    <img :src="HomeBanner" class="home-banner" alt="" />

    <div>
      <template v-if="loadingBooks">
        <SkeletonBookCard viewMode="two-rows" />
      </template>
      <template v-else>
        <BookCollection
          class="book-list-collection"
          :titleCollection="topBook"
          :typeCollection="CollectionType"
          :initialViewMode="'two-rows'"
          :initialBooks="BookCollectionData"
        />
      </template>
    </div>

    <OurService class="our-service" />
  </div>
</template>
<script lang="ts" setup>
import { onMounted, ref, computed } from "vue";
import { useRouter } from "vue-router";
import BookCollection from "@/components/BookCollection.vue";
import CategoryCollection from "@/components/CategoryCollection.vue";
import CommonMenu from "@/components/CommonMenu.vue";
import OurService from "@/components/OurService.vue";
import { HomeBanner, slideTop, slideTopSecond, slideTopThird } from "@/assets";
import type {
  ApiResponse,
  Author,
  Book,
  Category,
  Publisher,
} from "@/types/index";
import bookService from "@/services/bookService";
import categoryService from "@/services/categoryService";
import authorService from "@/services/authorService";
import publisherService from "@/services/publisherService";
import SkeletonBookCard from "@/components/SkeletonBookCard.vue";
import SkeletonCategoryCard from "@/components/SkeletonCategoryCard.vue";
import SkeletonCategoryCollection from "@/components/SkeletonCategoryCollection.vue";

const BookCollectionData = ref<Book[]>([]);
const mostBorrowedBookCollection = ref<Book[]>([]);
const categoriesCollection = ref<Category[]>([]);
const authorsCollection = ref<Author[]>([]);
const publishersCollection = ref<Publisher[]>([]);
const loadingBooks = ref(true);
const loadingCategories = ref(true);

const menuData = computed(() => categoriesCollection.value);
const router = useRouter();

const handleFilter = ({ id }: { id: number }) => {
  router.push({
    name: "Books",
    query: { categories_id: id },
  });
};

onMounted(async () => {
  loadingBooks.value = true;
  loadingCategories.value = true;

  try {
    const [books, mostBorrowedBook, categories, authors, publishers]: [
      ApiResponse<Book[]>,
      ApiResponse<Book[]>,
      ApiResponse<Category[]>,
      ApiResponse<Author[]>,
      ApiResponse<Publisher[]>,
    ] = await Promise.all([
      bookService.getBooks(),
      bookService.getMostBorrowedBooks(),
      categoryService.getCategories(),
      authorService.getAuthors(),
      publisherService.getPublishers(),
    ]);

    if (books.success == 1) {
      BookCollectionData.value = books.data;
    } else {
      throw new Error(books.message || "Failed to fetch books");
    }

    if (mostBorrowedBook.success == 1) {
      mostBorrowedBookCollection.value = mostBorrowedBook.data;
    } else {
      throw new Error(mostBorrowedBook.message || "Failed to fetch books");
    }

    if (categories.success == 1) {
      categoriesCollection.value = categories.data;
    } else {
      throw new Error(categories.message || "Failed to fetch categories");
    }

    if (authors.success == 1) {
      authorsCollection.value = authors.data;
    } else {
      throw new Error(authors.message || "Failed to fetch authors");
    }

    if (publishers.success == 1) {
      publishersCollection.value = publishers.data;
    } else {
      throw new Error(publishers.message || "Failed to fetch publishers");
    }
  } catch (error) {
    console.error(error);
  } finally {
    loadingBooks.value = false;
    loadingCategories.value = false;
  }
});

const carouselItems = ref([
  {
    imageUrl: slideTop,
    title: "iPhone 14 Series",
    description: "Up to 10% off Voucher",
  },
  {
    imageUrl: slideTopSecond,
    title: "iPhone 14 Series",
    description: "Up to 10% off Voucher",
  },
  {
    imageUrl: slideTopThird,
    title: "iPhone 14 Series",
    description: "Up to 10% off Voucher",
  },
]);

const CollectionType = ref("arrows");
const topBook = [{ title: "Today's", subtitle: "Book collection" }];
const mostBorrowedBook = [{ title: "Today's", subtitle: "Most Borrowed" }];
</script>
<style lang="scss" scoped>
.container {
  margin: 0 auto;
  max-width: 1170px;
  .header-content {
    .el-col {
      padding-top: 40px;
    }
  }

  .el-menu {
    overflow-y: scroll;
    max-height: 300px;
  }
  .el-menu-item,
  .el-sub-menu__title {
    --el-menu-base-level-padding: 0px;
  }

  .el-carousel__item {
    margin-bottom: 20px;
    img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
  }

  .carousel-item-content {
    text-align: center;

    h3 {
      margin-bottom: 10px;
    }
  }

  .el-menu-vertical {
    height: fit-content;
    overflow-y: scroll;
  }
  .top-books,
  .category-collection .book-list-collection {
    margin: 140px 0px 60px 0px;
    padding-bottom: 60px;
    border-bottom: 1px solid #e8e8e8;
  }
  .book-list-collection {
    height: 1000px;
    .carousel-wrapper {
      max-height: 344px;
    }
  }
  .our-service {
    margin-top: 250px;
  }
  .home-banner {
    width: 100%;
    height: 100%;
    object-fit: cover;
    margin: 140px 0px 70px 0px;
  }
}
@media (max-width: 768px) {
  .header-content {
    display: flex;
    flex-direction: column-reverse;
  }
  .top-books,
  .category-collection .book-list-collection {
    margin: 70px 0px 30px 0px;
    padding-bottom: 30px;
    border-bottom: 1px solid #e8e8e8;
  }
  .book-list-collection {
    .carousel-wrapper {
      height: auto;
      max-height: fit-content;
    }
  }
  .home-banner {
    margin: 70px 0px 35px 0px;
  }
  .our-service {
    margin-top: 125px;
  }
}

@media (max-width: 480px) {
  .top-books,
  .category-collection .book-list-collection {
    margin: 50px 0px 20px 0px;
    padding-bottom: 20px;
    border-bottom: 1px solid #e8e8e8;
  }
  .home-banner {
    margin: 50px 0px 25px 0px;
  }
  .our-service {
    margin-top: 100px;
  }
}
</style>
