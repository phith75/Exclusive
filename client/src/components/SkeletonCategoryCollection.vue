<template>
  <el-skeleton class="skeleton-header" :rows="1">
    <el-skeleton-item variant="text" class="skeleton-title" />
    <el-skeleton-item variant="text" class="skeleton-subtitle" />
  </el-skeleton>
  <el-row>
    <el-col class="skeleton-card" :span="3" :xs="11" v-for="n in 6" :key="n">
      <el-skeleton-item variant="image" class="skeleton-image" />
      <el-skeleton-item variant="text" class="skeleton-text" />
    </el-col>
  </el-row>
</template>

<script lang="ts" setup></script>

<style scoped>
.skeleton-header {
  width: 100%;
  max-width: 400px;
  margin-bottom: 60px;
  .skeleton-title {
    width: 30%;
    height: 50px;
    margin-bottom: 8px;
  }

  .skeleton-subtitle {
    width: 20%;
    height: 40px;
  }
}
.el-row {
  display: flex;
  gap: 30px;
  justify-content: flex-start;
}
.skeleton-card {
  min-width: 160px;
  min-height: 160px;
  border: 1px solid #e0e0e0;
  border-radius: 4px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.skeleton-image {
  width: 80px;
  height: 80px;
  margin-bottom: 16px;
}

.skeleton-text {
  width: 60%;
  height: 20px;
}
</style>
