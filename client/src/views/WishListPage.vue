<template>
  <div class="container">
    <SkeletonBookCard :viewMode="'one-row'" v-if="loadingBooks" />
    <BookCollection
      v-else
      class="wish-list-books"
      :titleCollection="wishList"
      :typeCollection="CollectionType"
      :initialViewMode="'one-row'"
      :initialBooks="wishListBooks"
    />
    <SkeletonBookCard :viewMode="'one-row'" v-if="loadingBooks" />

    <BookCollection
      v-else
      :titleCollection="related"
      class="related-books"
      :typeCollection="CollectionType"
      :initialViewMode="'one-row'"
      :initialBooks="relatedBooks"
    />
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from "vue";
import BookCollection from "@/components/BookCollection.vue";
import wishlistService from "@/services/wishlistService";
import type { Book } from "@/types";
import SkeletonBookCard from "@/components/SkeletonBookCard.vue";
const CollectionType = ref("button");
const loadingBooks = ref(true);
const wishList = [
  {
    title: "Wish List",
    subtitle: "",
  },
];
const related = [
  {
    title: "Related Books",
    subtitle: "",
  },
];

const wishListBooks = ref<Book[]>([]);
const relatedBooks = ref<Book[]>([]);

onMounted(async () => {
  try {
    loadingBooks.value = true;

    const [wishlistResponse, suggestionResponse] = await Promise.all([
      wishlistService.getWishlistsByUser(),
      wishlistService.suggestForUser(),
    ]);

    if (wishlistResponse.success === 1) {
      wishListBooks.value = wishlistResponse.data;
    } else {
      console.error(
        "Failed to fetch wishlist books:",
        wishlistResponse.message
      );
    }

    if (suggestionResponse.success === 1) {
      relatedBooks.value = suggestionResponse.data;
    } else {
      console.error(
        "Failed to fetch related books:",
        suggestionResponse.message
      );
    }
  } catch (error) {
    console.error("Error fetching data:", error);
  } finally {
    loadingBooks.value = false;
  }
});
</script>

<style scoped lang="scss">
.container {
  margin: auto;
  padding-top: 40px;
  max-width: 1170px;
}
</style>
