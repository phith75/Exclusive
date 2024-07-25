<template>
  <div class="pagination">
    <div class="pagination_content">
      <div class="pagination-left" @click="prevPage">
        <img src="@/assets/images/arrow-left.png" alt="arrow-left" />
      </div>
      <div class="pagination-center" id="pagination-render">
        <p
          v-for="page in paginatedPages"
          :key="page"
          :class="['page-item', { 'active-paginate': page === currentPage }]"
          @click="goToPage(page)"
        >
          {{ page }}
        </p>
      </div>
      <div class="pagination-right" @click="nextPage">
        <img src="@/assets/images/arrow-right.png" alt="arrow-right" />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { defineProps, defineEmits, computed } from "vue";

const props = defineProps({
  currentPage: {
    type: Number,
    required: true,
  },
  totalPages: {
    type: Number,
    required: true,
  },
});

const emit = defineEmits(["goToPage", "prevPage", "nextPage"]);

const goToPage = (page: string | number) => {
  emit("goToPage", page);
};

const prevPage = () => {
  if (props.currentPage > 1) {
    emit("prevPage");
  }
};

const nextPage = () => {
  if (props.currentPage < props.totalPages) {
    emit("nextPage");
  }
};

const paginatedPages = computed(() => {
  const { currentPage, totalPages } = props;
  const pages = [];

  if (totalPages <= 6) {
    for (let i = 1; i <= totalPages; i++) {
      pages.push(i);
    }
  } else {
    if (currentPage <= 3) {
      pages.push(1, 2, 3, 4, "...", totalPages);
    } else if (currentPage > 3 && currentPage < totalPages - 2) {
      pages.push(
        1,
        "...",
        currentPage - 1,
        currentPage,
        currentPage + 1,
        "...",
        totalPages
      );
    } else {
      pages.push(
        1,
        "...",
        totalPages - 3,
        totalPages - 2,
        totalPages - 1,
        totalPages
      );
    }
  }

  return pages;
});
</script>

<style scoped></style>
