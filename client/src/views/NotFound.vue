<script lang="ts" setup></script>

<template>
  <el-container class="not-found">
    <el-breadcrumb separator="/">
      <el-breadcrumb-item :to="{ name: 'home' }">Home</el-breadcrumb-item>
      <el-breadcrumb-item>404 Error</el-breadcrumb-item>
    </el-breadcrumb>
    <div class="content">
      <el-text size="large" tag="h1">404 Not Found</el-text>
      <el-text tag="p"
        >Your visited page not found. You may go home page.</el-text
      >
      <el-button @click="$router.push({ name: 'home' })"
        >Back to home page</el-button
      >
    </div>
  </el-container>
</template>

<style lang="scss" scoped>
.not-found {
  margin: 40px auto 0px auto;

  .content {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    margin: 3rem auto;
    gap: 2.5rem;

    h1 {
      color: var(--primary-color-light);
      font-family: "Inter", sans-serif;
      font-size: 6.875rem;
      font-style: normal;
      font-weight: 500;
      line-height: 7.1875rem;
      letter-spacing: 0.20625rem;
    }

    p {
      color: var(--primary-color-light);
      font-family: "Inter", sans-serif;
      font-size: 1rem;
      font-style: normal;
      font-weight: 400;
      line-height: 1.5rem;
    }

    .el-button {
      height: 3.5rem;
      width: 15.87rem;
      display: inline-flex;
      padding: 1rem 3rem;
      justify-content: center;
      align-items: center;
      gap: 0.625rem;
      border-radius: 0.25rem;
      background: var(--Button2, rgba(255, 69, 0, 0.78));
      margin: 1rem auto;
      color: var(--second-color-light);
      font-family: "Poppins", sans-serif;
      font-size: 1rem;
      font-style: normal;
      font-weight: 500;
      line-height: 1.5rem;
    }
  }
}

@media screen and (max-width: 768px) {
  .not-found {
    .content {
      h1 {
        font-size: 3rem;
        line-height: 4rem;
      }

      .el-button {
        width: 100%;
      }
    }
  }
}
</style>
