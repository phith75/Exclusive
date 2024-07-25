<script lang="ts" setup>
import AppHeader from "@/components/HeaderComponent.vue";
import AppFooter from "@/components/FooterComponent.vue";
</script>

<template>
  <div class="app">
    <AppHeader class="app-header" />
    <el-main>
      <router-view />
    </el-main>
    <AppFooter />
    <el-backtop :right="20" :bottom="20" />
  </div>
</template>

<style lang="scss" scoped>
.app {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  z-index: 0;

  .app-header {
    position: sticky;
    top: 0;
    z-index: 100;
  }

  .el-main {
    padding-bottom: 140px;
    min-height: 300px;
  }

  @media (max-width: 1200px) {
    .el-main {
      padding-bottom: 120px;
    }
  }

  @media (max-width: 992px) {
    .el-main {
      padding-bottom: 100px;
    }
    .app-header {
      position: sticky;
    }
  }

  @media (max-width: 768px) {
    .el-main {
      padding: 0 10px 60px 10px;
    }
    .app {
      flex-direction: column;
    }
  }

  @media (max-width: 576px) {
    .el-main {
      padding-bottom: 60px;
    }
  }
}
</style>
