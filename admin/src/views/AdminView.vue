<template>
  <div class="container">
    <AppAside class="app_aside" />
    <div class="app">
      <AppHeader class="app_header" />
      <main>
        <RouterView />
      </main>
    </div>
  </div>
</template>

<script setup lang="ts" generic="">
import { RouterView } from "vue-router";
import AppAside from "@/components/layouts/AppAside.vue";
import AppHeader from "@/components/layouts/AppHeader.vue";
</script>

<style scoped></style>
