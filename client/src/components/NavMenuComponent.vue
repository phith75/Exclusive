<template>
  <div class="sidebar">
    <h3>Manage My Account</h3>
    <ul>
      <li>
        <router-link :to="{ path: '/my-profile' }" class="active"
          >My Profile</router-link
        >
      </li>
      <li v-if="googleId == ''">
        <router-link :to="{ path: '/change-password' }" class="active"
          >Change Password</router-link
        >
      </li>
    </ul>
    <h3>My Orders</h3>
    <ul>
      <li>
        <router-link :to="{ path: '/ticket-page' }">My Tickets</router-link>
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import { useAuthStore } from "@/stores/authStore";
import { ref } from "vue";
const authStore = useAuthStore();

const user = ref(authStore.getUser);
const googleId = ref<string>(user.value?.google_id || "");
</script>

<style scoped lang="scss">
.sidebar {
  width: 25%;
  padding-right: 20px;

  h3 {
    font-size: 18px;
    margin-bottom: 10px;
  }

  ul {
    list-style: none;
    padding: 0;

    li {
      margin-bottom: 10px;
      margin-left: 35px;
      .router-link-active {
        color: var(--primary-color);
        font-weight: bold;
      }

      a {
        color: var(--text-color);
        text-decoration: none;
        font-size: 16px;

        &:hover {
          color: var(--primary-color);
        }
      }
    }
  }
}
@media (max-width: 768px) {
  .sidebar {
    width: 100%;
    padding-right: 0;
  }
}
</style>
