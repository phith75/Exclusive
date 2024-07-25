<template>
  <el-menu
    :default-active="activeIndex"
    class="el-menu-vertical category-menu"
    @select="handleSelect"
    v-loading="loading"
  >
    <el-menu-item
      v-for="(category, index) in menuItems"
      :key="index"
      :index="String(index + 1)"
      :data-id="category.id"
    >
      <span>{{ category.name }}</span>
    </el-menu-item>
  </el-menu>
</template>

<script lang="ts" setup>
import { ref } from "vue";

interface Category {
  id: string;
  name: string;
  slug: string;
}

const props = defineProps<{
  menuItems: Category[];
  loading: boolean;
}>();

const emit = defineEmits<{
  (e: "filter", filter: { type: "categories"; id: string }): void;
}>();

const activeIndex = ref<string | null>(null);

const handleSelect = (index: string) => {
  const category = props.menuItems[Number(index) - 1];
  if (activeIndex.value === index) {
    activeIndex.value = null;
    emit("filter", { type: "categories", id: "" });
  } else {
    activeIndex.value = index;
    emit("filter", { type: "categories", id: category.id });
  }
};
</script>

<style scoped>
.el-menu-vertical {
  overflow-y: auto;
  max-height: 1560px;
}
@media screen and (max-width: 768px) {
  .el-menu-vertical {
    max-height: 500px;
    overflow-y: auto;
  }
}
</style>
