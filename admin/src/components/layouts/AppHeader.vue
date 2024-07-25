<template>
  <header>
    <div class="header-left">
      <div class="header-left-nav-icon" @click="toggleSidebar">
        <img src="@/assets/images/Icon.svg" alt="nav-icon" />
      </div>
    </div>
    <div class="header-right">
      <div class="header-right-user">
        <img src="@/assets/images/avartar.png" alt="user" />
        <div class="header-right-user-title" >
          <h4>{{ userDecode.name }}</h4>
          <img :src="More" alt="" @click="toggleDropdown" />
        </div>
        <div v-if="showDropdown" class="dropdown">
          <ul>
            <li @click="logout">Logout</li>
          </ul>
        </div>
      </div>
    </div>
  </header>
</template>
<script setup lang="ts">
import { ref, computed } from "vue";
import { useSidebar } from "@/composables/useSidebar.ts";
import { useAuthStore } from "@/stores/authStore";
import { useRouter } from "vue-router";
import { toast } from "vue3-toastify";
import More from "@/assets/images/More.png";

const { toggleSidebar } = useSidebar();
const showDropdown = ref(false);

const authStore = useAuthStore();
const router = useRouter();

const user = computed(() => authStore.getUser);
const userDecode = computed(() => {
  if (user.value) {
    return JSON.parse(user.value);
  }
  return {};
});

const toggleDropdown = () => {
  showDropdown.value = !showDropdown.value;
};

const logout = () => {
  authStore.logout();
  showDropdown.value = false;
};
</script>

<style scoped>
.header-right-user {
  position: relative;
  cursor: pointer;
}

.dropdown {
  position: absolute;
  top: 100%;
  right: 0;
  background-color: #fff;
  border: 1px solid #ccc;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  z-index: 1000;
}

.dropdown ul {
  list-style: none;
  margin: 0;
  padding: 0;
}

.dropdown ul li {
  padding: 10px 20px;
  cursor: pointer;
}

.dropdown ul li:hover {
  background-color: #f0f0f0;
}
</style>
