<template>
  <el-skeleton animated>
    <template #template>
      <div class="skeleton-menu">
        <el-skeleton-item
          variant="text"
          class="skeleton-title"
          v-for="n in 6"
          :key="n"
        />
        <div v-for="n in 6" :key="`sub-${n}`" class="skeleton-submenu"></div>
      </div>
    </template>
  </el-skeleton>
</template>

<script lang="ts" setup></script>

<style scoped>
.skeleton-menu {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.skeleton-title {
  width: 100%;
  height: 20px;
  margin-bottom: 16px;
}

.skeleton-submenu {
  display: flex;
  flex-direction: column;
  gap: 8px;
  padding-left: 20px;
}
</style>
