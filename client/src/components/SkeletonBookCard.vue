<template>
  <div class="skeleton-book-title" justify="space-between">
    <el-skeleton class="skeleton-header" :rows="1">
      <el-skeleton-item variant="text" class="skeleton-title" />
      <el-skeleton-item variant="text" class="skeleton-subtitle" />
    </el-skeleton>
    <el-row>
      <el-col :xs="24" :sm="12" :md="8" :lg="6" v-for="n in 4" :key="n">
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
    <el-row v-if="viewMode === 'two-rows'" justify="space-between">
      <el-col
        :xs="24"
        :sm="12"
        :md="8"
        :lg="6"
        v-for="n in 4"
        :key="`two-rows-${n}`"
      >
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
      <el-skeleton> </el-skeleton>
    </el-row>
  </div>
</template>
<script lang="ts" setup>
import { ref, watch, defineProps } from "vue";

const props = defineProps<{
  viewMode: string;
}>();

const viewMode = ref(props.viewMode);

watch(
  () => props.viewMode,
  (newViewMode) => {
    viewMode.value = newViewMode;
  }
);
</script>
<style scoped lang="scss">
.skeleton-book-title {
  margin: 140px 0px 60px 0px;
}
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
  justify-content: space-between;
}
.skeleton-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding:0px 16px ;
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
</style>
