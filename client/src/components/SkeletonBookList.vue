<template>
  <el-row v-for="n in 4" :key="`one-rows-${n}`">
    <el-col :xs="24" :md="8" :lg="8" v-for="n in 3" :key="`two-rows-${n}`">
      <el-skeleton :rows="5" animated>
        <template #template>
          <div class="skeleton-card">
            <el-skeleton-item variant="image" class="skeleton-image" />
            <el-skeleton-item variant="text" class="skeleton-text" />
            <el-skeleton-item variant="text" class="skeleton-text" />
            <el-skeleton-item variant="text" class="skeleton-star" />
          </div>
        </template>
      </el-skeleton>
    </el-col>
  </el-row>
</template>
<script lang="ts" setup></script>
<style scoped lang="scss">
.el-row {
  display: flex;
  justify-content: space-between;
}
.skeleton-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 0px 16px;
  margin-left: 2px;
  height: 372px;
  min-width: 250px;
  width: 100%;
  .skeleton-image {
    width: 100%;
    height: 267px;
    margin-bottom: 16px;
  }

  .skeleton-text {
    width: 100%;
    height: 20px;
    margin-bottom: 8px;
  }
  .skeleton-star {
    height: 20px;
    margin-bottom: 8px;
    left: 5px;
  }
}
@media (max-width: 768px) {
  .skeleton-card {
    padding: 0 42px;
  }
}
</style>
