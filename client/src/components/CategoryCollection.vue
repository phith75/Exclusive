<template>
  <div class="browse-categories">
    <div class="browse-categories-top">
      <TitleCollection title="Categories" subtitle="Collection" />
      <div class="carousel-arrows">
        <el-button @click="prevSlide" circle>
          <img :src="leftArrow" alt="Previous" />
        </el-button>
        <el-button @click="nextSlide" circle>
          <img :src="rightArrow" alt="Next" />
        </el-button>
      </div>
    </div>
    <div class="carousel-wrapper">
      <el-carousel
        v-if="displayedCategories.length > 0"
        ref="categoryCarousel"
        :autoplay="true"
        height="150px"
        arrow="never"
        indicator-position="none"
      >
        <el-carousel-item
          v-for="(group, index) in displayedCategories"
          :key="index"
        >
          <div class="category-group">
            <el-card
              v-for="(category, subIndex) in group"
              :key="subIndex"
              class="category-card"
              shadow="hover"
            >
              <div class="icon-container">
                <el-image :src="getImageUrl(category.image)" class="icon" />
              </div>
              <div class="category-name">{{ category.name }}</div>
            </el-card>
          </div>
        </el-carousel-item>
      </el-carousel>
      <el-empty v-else></el-empty>
    </div>
    <div class="line"></div>
  </div>
</template>
<script lang="ts" setup>
import { ref, watch, onMounted, onBeforeUnmount } from "vue";
import { leftArrow, rightArrow } from "@/assets";
import TitleCollection from "@/components/TitleCollection.vue";
import type { Category } from "@/types";

const props = defineProps<{
  initCategories: Category[];
}>();

const categoryData = ref<Category[]>([...props.initCategories]);
const displayedCategories = ref<Category[][]>([]);
const itemsPerGroup = ref(6); // Số danh mục hiển thị mỗi nhóm

const getImageUrl = (imagePath: string) => {
  if (imagePath.startsWith("https")) {
    return imagePath;
  } else {
    return `${import.meta.env.VITE_IMAGE_URL}${imagePath}`;
  }
};

const updateDisplayedCategories = () => {
  if (window.innerWidth <= 480) {
    itemsPerGroup.value = 2;
  } else {
    itemsPerGroup.value = 6;
  }
  const groups = [];
  for (let i = 0; i < categoryData.value.length; i += itemsPerGroup.value) {
    groups.push(categoryData.value.slice(i, i + itemsPerGroup.value));
  }
  displayedCategories.value = groups;
};

watch(
  () => props.initCategories,
  (newValue) => {
    categoryData.value = [...newValue];
    updateDisplayedCategories();
  }
);

onMounted(() => {
  updateDisplayedCategories();

  window.addEventListener("resize", updateDisplayedCategories);
  watch(categoryData, () => {
    updateDisplayedCategories();
  });
});

const categoryCarousel = ref();

const prevSlide = (isAutoplay = false) => {
  const lastCategory = categoryData.value.pop();
  if (lastCategory) {
    categoryData.value.unshift(lastCategory);
  }
  updateDisplayedCategories();
};

const nextSlide = (isAutoplay = false) => {
  const firstCategory = categoryData.value.shift();
  if (firstCategory) {
    categoryData.value.push(firstCategory);
  }
  updateDisplayedCategories();
};
</script>
<style lang="scss" scoped>
.browse-categories-top {
  display: flex;
  align-items: end;
}
.line {
  margin-top: 50px;
  border-bottom: 1px solid #e8e8e8;
}
.browse-categories {
  margin-top: 40px;
  margin-bottom: 20px;
  padding: 20px;

  &-top {
    display: flex;
    justify-content: space-between;
    margin-bottom: 60px;

    .browse-categories-title {
      font-size: 28px;
      font-weight: bold;
      margin-bottom: 10px;
    }

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
    padding: 10px 0px;
    height: 180px;
  }

  .category-group {
    display: flex;
    justify-content: space-between;
    gap: 30px;

    .category-card {
      flex: 1;
      height: 150px;
      max-width: 170px;
      border-radius: 4px;
      overflow: hidden;
      cursor: pointer;
      position: relative;
      text-align: center;

      .icon-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 50px;

        .icon {
          width: 50px;
          height: 50px;
          object-fit: contain;
          display: block;
        }
      }

      .category-name {
        margin-top: 10px;
        font-size: 16px;
        font-weight: bold;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }
    }
  }
}

@media (max-width: 768px) {
  .category-group {
    flex-wrap: wrap;
    gap: 15px;
  }
}

@media (max-width: 480px) {
  .category-group {
    flex-wrap: wrap;
    gap: 15px;
  }

  .category-card {
    width: calc(50% - 15px);
  }
}
</style>
