<template>
  <div class="section-header">
    <div class="header-icon-text">
      <div class="color-header"></div>
      <span class="header-text">{{ title }}</span>
    </div>
    <h2 class="section-title">{{ subtitle }}</h2>
  </div>
</template>

<script lang="ts" setup>
defineProps({
  title: {
    type: String,
    required: true,
  },
  subtitle: {
    type: String,
    required: true,
  },
});
</script>

<style scoped lang="scss">
.section-header {
  display: flex;
  flex-direction: column;
  align-items: flex-start;

  .header-icon-text {
    display: flex;
    align-items: center;
    margin-bottom: 10px;

    .color-header {
      width: 20px;
      height: 40px;
      border-radius: 4px;
      background-color: #ff4500;
      margin-right: 10px;
    }

    .header-text {
      font-size: 14px;
      color: #ff4500;
    }
  }

  .section-title {
    font-size: 28px;
    font-weight: bold;
  }
}
</style>
