<template>
  <div class="header">
    <el-row class="top-header">
      <el-container>
        <el-row align="middle" justify="end" type="flex">
          <el-col :lg="22" :sm="21" :xs="19" class="slogan">
            <p>Sincerity・Sharing・Arigatou・Respect</p>
          </el-col>
          <el-col :lg="2" :sm="3" :xs="5">
            <el-dropdown>
              <span class="el-dropdown-link">
                English
                <el-icon class="el-icon--right">
                  <arrow-down />
                </el-icon>
              </span>
              <template #dropdown>
                <el-dropdown-menu>
                  <el-dropdown-item>English</el-dropdown-item>
                  <el-dropdown-item>Vietnamese</el-dropdown-item>
                </el-dropdown-menu>
              </template>
            </el-dropdown>
          </el-col>
        </el-row>
      </el-container>
    </el-row>
    <el-header class="header-menu">
      <el-menu
        :default-active="activeIndex"
        :ellipsis="false"
        :popper-offset="10"
        active-text-color="#000"
        background-color="transparent"
        class="nav-menu"
        close-on-click-outside
        collapse-transition
        mode="horizontal"
        text-color="#000"
      >
        <el-image
          :src="Logo"
          alt="Element logo"
          class="logo"
          @click="goToHome"
        />
        <el-row class="link-item">
          <el-menu-item index="1">
            <router-link :to="{ name: 'Homepage' }">Home</router-link>
          </el-menu-item>
          <el-menu-item index="2">
            <router-link :to="{ name: 'Books' }">Books</router-link>
          </el-menu-item>
          <el-menu-item index="3">
            <router-link :to="{ name: 'Contact' }">Contact</router-link>
          </el-menu-item>
          <el-menu-item index="4">
            <router-link :to="{ name: 'About' }">About</router-link>
          </el-menu-item>
          <el-menu-item index="5" v-if="!isLogin">
            <router-link :to="{ name: 'Register' }">Sign Up</router-link>
          </el-menu-item>
        </el-row>
        <el-row class="action">
          <el-form class="form" @submit.native.prevent="handleSearch">
            <el-input
              v-model="search"
              class="search-input"
              placeholder="Search"
              @keyup.enter.native="handleSearch"
            >
              <template #suffix>
                <el-icon>
                  <Search />
                </el-icon>
              </template>
            </el-input>
          </el-form>
          <el-badge
            v-if="isLogin"
            :value="wishlistCount"
            class="wishlist-info"
            @click="$router.push({ name: 'Wishlist' })"
          >
            <el-image
              :src="Wishlist"
              alt="Wishlist Icon"
              class="wishlist-icon"
            />
          </el-badge>
          <el-badge
            @click="$router.push({ name: 'Cart' })"
            :value="cartCount"
            class="cart-info"
          >
            <el-image :src="CartIcon" alt="Cart Icon" class="cart-icon" />
          </el-badge>
          <el-dropdown
            v-if="isLogin"
            ref="profile"
            placement="bottom-end"
            class="user-action"
            trigger="contextmenu"
          >
            <el-image
              :src="ProfileIcon"
              alt="Profile Icon"
              class="profile-icon"
              @click="showClick"
            />
            <template #dropdown>
              <el-dropdown-menu placement="bottom-end" class="dropdown">
                <el-dropdown-item>
                  <router-link :to="{ name: 'MyProfile' }">
                    <el-image :src="UserIcon" alt="User Icon" />
                    <span>Manage My Account</span>
                  </router-link>
                </el-dropdown-item>
                <el-dropdown-item>
                  <router-link :to="{ name: 'TicketPage' }">
                    <el-image :src="TicketIcon" alt="Ticket Icon" />
                    <span>My Ticket</span>
                  </router-link>
                </el-dropdown-item>
                <el-dropdown-item>
                  <router-link :to="{ name: 'Wishlist' }">
                    <el-image :src="WishlistDropdown" alt="Ticket Icon" />
                    <span>My Wishlist</span>
                  </router-link>
                </el-dropdown-item>
                <el-dropdown-item @click="handleLogout">
                  <router-link to="#">
                    <el-image :src="LogoutIcon" alt="Logout Icon" />
                    <span>Logout</span>
                  </router-link>
                </el-dropdown-item>
              </el-dropdown-menu>
            </template>
          </el-dropdown>
        </el-row>
      </el-menu>
      <div class="menu-action-moblie">
        <el-row class="action action-moblie">
          <el-form class="form" @submit.native.prevent="handleSearch">
            <el-input
              v-model="search"
              class="search-input"
              placeholder="Search"
              @keyup.enter.native="handleSearch"
            >
              <template #suffix>
                <el-icon>
                  <Search />
                </el-icon>
              </template>
            </el-input>
          </el-form>
        </el-row>
        <el-button class="toggle-menu" @click="drawer = true">
          <el-icon>
            <Fold />
          </el-icon>
        </el-button>
        <el-drawer v-model="drawer" direction="ltr" size="95%">
          <el-menu
            :default-active="activeIndex"
            :ellipsis="false"
            :popper-offset="16"
            active-text-color="#000"
            background-color="transparent"
            class="nav-menu-mobile"
            close-on-click-outside
            collapse-transition
            mode="vertical"
            text-color="#000"
          >
            <el-image
              :src="Logo"
              alt="Element logo"
              class="logo"
              @click="goToHome"
            />
            <el-row gutter="6" justify="center">
              <el-menu-item index="1">
                <router-link
                  :to="{ name: 'Homepage' }"
                  @click.native="closeDrawer"
                  >Home</router-link
                >
              </el-menu-item>
              <el-menu-item index="2">
                <router-link :to="{ name: 'Books' }" @click.native="closeDrawer"
                  >Books</router-link
                >
              </el-menu-item>
              <el-menu-item index="3">
                <router-link
                  :to="{ name: 'Contact' }"
                  @click.native="closeDrawer"
                  >Contact</router-link
                >
              </el-menu-item>
              <el-menu-item index="4">
                <router-link :to="{ name: 'About' }" @click.native="closeDrawer"
                  >About</router-link
                >
              </el-menu-item>
              <el-menu-item index="5" v-if="!isLogin">
                <router-link
                  :to="{ name: 'Register' }"
                  @click.native="closeDrawer"
                  >Sign Up</router-link
                >
              </el-menu-item>
              <div v-else>
                <el-menu-item index="7">
                  <router-link
                    :to="{ name: 'MyProfile' }"
                    @click.native="closeDrawer"
                    >My Profile</router-link
                  >
                </el-menu-item>
                <el-menu-item index="8">
                  <router-link
                    :to="{ name: 'ChangePassword' }"
                    @click.native="closeDrawer"
                    >Change Password</router-link
                  >
                </el-menu-item>
                <el-menu-item index="9">
                  <router-link
                    :to="{ name: 'TicketPage' }"
                    @click.native="closeDrawer"
                    >My Ticket</router-link
                  >
                </el-menu-item>
                <el-menu-item index="10" class="badge-moblie">
                  <el-badge
                    @click="$router.push({ name: 'Cart' })"
                    :value="cartCount"
                  >
                    <router-link
                      :to="{ name: 'Cart' }"
                      @click.native="closeDrawer"
                      >My Cart</router-link
                    >
                  </el-badge>
                </el-menu-item>
                <el-menu-item index="6" class="badge-moblie">
                  <el-badge
                    v-if="isLogin"
                    :value="wishlistCount"
                    @click="$router.push({ name: 'Wishlist' })"
                  >
                    <router-link
                      :to="{ name: 'Wishlist' }"
                      @click.native="closeDrawer"
                      >Wishlist</router-link
                    >
                  </el-badge>
                </el-menu-item>
                <el-menu-item
                  class="logout-mobile"
                  @click="handleLogout"
                  index="10"
                >
                  <router-link to="#" @click.native="closeDrawer"
                    >Logout</router-link
                  >
                </el-menu-item>
              </div>
            </el-row>
          </el-menu>
        </el-drawer>
      </div>
    </el-header>
  </div>
</template>

<script lang="ts" setup>
import { computed, ref, onMounted, watch } from "vue";
import { ArrowDown, Search } from "@element-plus/icons-vue";
import {
  CartIcon,
  Logo,
  LogoutIcon,
  ProfileIcon,
  TicketIcon,
  UserIcon,
  Wishlist,
  WishlistDropdown,
} from "@/assets";
import type { DropdownInstance } from "element-plus";
import { useAuthStore } from "@/stores/authStore";
import { useRouter } from "vue-router";
import { toast } from "vue3-toastify";
import { useCartStore } from "@/stores/cartStore";
import wishlistService from "@/services/wishlistService";
import bookService from "@/services/bookService";

const activeIndex = ref("1");
const search = ref("");
const drawer = ref(false);
const profile = ref<DropdownInstance>();
const authStore = useAuthStore();
const cartStore = useCartStore();
const router = useRouter();
const isLogin = computed(() => authStore.isLogin);
const cartCount = computed(() => cartStore.totalQuantity);
const wishlistCount = ref<number>(0);

const getWishlist = async () => {
  try {
    const response = await wishlistService.getWishlists();
    if (response.success === 1) {
      authStore.setWishlist(response.data);
      wishlistCount.value = response.data.length;
    }
  } catch (error) {
    console.error(error);
  }
};
const closeDrawer = () => {
  drawer.value = false;
};

watch(
  () => authStore.wishlistUser,
  (newWishlist) => {
    wishlistCount.value = newWishlist.length;
  },
  { immediate: true }
);

function showClick() {
  if (!profile.value) return;
  profile.value.handleOpen();
}

async function handleLogout() {
  try {
    await authStore.logout();
  } catch (error: any) {
    toast.error(error.data.error.message);
  }
}
const querySearch = async (
  queryString: string,
  cb: (results: any[]) => void
) => {
  try {
    const response = await bookService.getBooks(1, queryString, "", "", "");
    if (response.success === 1) {
      const suggestions = response.data.map((book: any) => ({
        value: book.name,
        id: book.id,
        thumbnail: book.thumbnail,
      }));
      cb(suggestions);
    } else {
      console.error("Failed to fetch suggestions:", response.message);
    }
  } catch (error) {
    console.error("Error fetching suggestions:", error);
  }
};
const handleSelect = (item: any) => {
  router.push({ name: "BookDetail", params: { id: item.id } });
};
const goToHome = () => {
  router.push({ name: "Homepage" });
};

const handleSearch = () => {
  const query = { ...router.currentRoute.value.query };
  if (search.value) {
    query.keywords = search.value;
  } else {
    delete query.keywords;
  }
  router.push({ name: "Books", query });
};

onMounted(() => {
  getWishlist();
});
</script>

<style lang="scss" scoped>
:deep(.el-tooltip__trigger) {
  color: white !important;
}
.dropdown {
  background-image: (
    linear-gradient(rgba(0, 0, 0, 0.5), rgba(167, 159, 168, 0.5))
  );

  a {
    color: white;
    display: flex;
    gap: 1rem;
    flex: start;
  }
  :deep(.el-dropdown-menu__item) {
    color: var(--Text, white);
  }
  :deep(.el-dropdown-menu__item:hover) {
    background-color: transparent;
    color: var(--Text, #000000);

    a {
      color: var(--Text, #000000);
    }
  }
  .el-dropdown {
    color: var(--Text, #fafafa);
    font-size: 0.875rem;
    font-weight: 400;
    line-height: 1.3125rem;
    cursor: pointer;
    &:focus,
    &:hover,
    &:active {
      outline: none;
      border: none;
      background-color: transparent;
      color: var(--Text, #fafafa);
    }
  }
}
.header {
  background-color: #ffffff;
  .logo {
    cursor: pointer;
  }
  .top-header {
    background-color: var(--primary-color);
    color: #fafafa;
    font-size: 0.875rem;
    font-weight: 400;
    line-height: 1.3125rem;
    padding: 1rem 0;

    .el-container {
      margin: 0 auto;
      .slogan {
        text-align: center;
        a {
          color: #fafafa;
          text-decoration: underline;
          font-weight: 600;
        }
      }

      .el-row {
        width: 100%;
        flex-wrap: nowrap;
      }
    }
  }

  .header-menu {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid rgba(255, 69, 0, 0.78);
    padding: 0;
    padding: 40px 0px;
    .nav-menu-mobile {
      .el-row {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
      }
    }

    .el-menu--horizontal {
      display: flex;
      max-width: 1170px;
      justify-content: space-between;
      width: 100%;
      align-items: center;
      height: 3rem;
      margin: 0 auto;
      .el-image {
        width: 7.375rem;
        height: 1.5rem;
      }

      .el-menu-item {
        color: var(--primary-color-light, #000);
        text-align: center;
        font-size: 1rem;
        font-weight: 400;
        border: none;
        padding: 0 10px;

        a {
          color: var(--primary-color-light);
          text-align: center;
          font-size: 1rem;
          font-weight: 400;

          &:hover {
            border-bottom: 1px solid var(--primary-color-light, #000);
          }
        }

        &:hover,
        .is-active {
          background-color: transparent;
          border: none;
        }

        &:not(.is-disabled):focus,
        &:not(.is-disabled):hover {
          background-color: transparent;
        }
      }

      .action {
        display: flex;
        align-items: center;
        gap: 1rem;

        .cart-info,
        .wishlist-info {
          margin-bottom: -0.5rem;
        }

        .wishlist-icon,
        .cart-icon,
        .profile-icon {
          width: 2rem;
          height: 2rem;
          cursor: pointer;
        }

        .form {
          display: flex;
          align-items: center;
          justify-content: center;
          gap: 0.5rem;
          border-radius: 0.25rem;
          background: var(--Secondary, #f5f5f5);
          padding: 0.4rem;

          .el-icon {
            cursor: pointer;
            margin: 0 0.5rem;
          }

          .el-input {
            .el-input__wrapper {
              background-color: transparent;
              border: none;
              box-shadow: none;

              &:focus,
              &:hover,
              &.is-focus {
                outline: none;
                border: none;
                box-shadow: none;
              }
            }
          }
        }
      }

      &.el-menu {
        border: none;
      }
    }

    .menu-action-moblie {
      display: none;
    }
  }

  @media (max-width: 768px) {
    .logout-mobile {
      left: -0.9%;
      position: fixed;
      bottom: 0;
      right: 5%;
      background-color: var(--primary-color);
      a {
        position: fixed;
        bottom: 0;
        font-size: 14px !important;
        left: 41.8%;
        color: #fafafa !important;
      }
    }
    .top-header {
      padding: 0.5rem 0;

      .slogan {
        font-size: 0.75rem;
        line-height: 1rem;
      }
    }
    .action-moblie {
      display: flex;
      width: 100%;
      .search-button {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 50%;
        background-color: rgba(245, 245, 245, 1);
      }
    }

    .el-header {
      padding: 0px 12px;

      justify-items: center;
      align-items: center;

      .el-menu--horizontal {
        .el-row,
        .el-form {
          display: none;
        }
      }

      .menu-action-moblie {
        display: flex;
        gap: 1rem;
        justify-content: space-between; /* Spread elements evenly */
        width: 100%; /* Occupy full width */
      }

      .search-input {
        background-color: #e9e8e8;
        border-radius: 5px;
      }
      .el-button {
        background-color: transparent;
        border: 1px solid var(--primary-color-light);
        border-radius: 0.25rem;
        color: var(--primary-color-light);
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5rem;

        &:focus {
          background-color: transparent;
        }
      }

      .el-drawer {
        .el-menu {
          border: none;
          display: flex;
          flex-direction: column;

          align-items: start;
        }

        .el-menu-item {
          align-self: start;
          border: none;
          padding: 0px !important;
          margin-left: 4px;
          a {
            color: var(--primary-color-light);
            font-size: 1.1rem;
            font-weight: 400;

            &:hover {
              border-bottom: 1px solid var(--primary-color-light, #000);
            }
          }

          &:hover,
          .is-active {
            background-color: transparent;
            border: none;
          }

          &:not(.is-disabled):focus,
          &:not(.is-disabled):hover {
            background-color: transparent;
          }
        }
        .cart-info,
        .user-action {
          margin-bottom: 1rem;
          align-self: center;
        }
        .cart-info {
          margin-top: 0.8rem;
        }
        .action {
          display: flex;
          align-items: center;
          gap: 0.825rem;
          flex-wrap: nowrap;

          .wishlist-icon,
          .cart-icon,
          .profile-icon {
            width: 2.5rem !important;
            height: 2.5rem !important;
          }

          .form {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.625rem;
            border-radius: 0.25rem;
            background: var(--Secondary, #f5f5f5);
            padding: 0.4rem;

            .el-icon {
              margin: 0 0.5rem;
            }

            .el-input {
              .el-input__wrapper {
                background-color: transparent;
                border: none;
                box-shadow: none;

                &:focus,
                &:hover &:focus,
                &:hover,
                &.is-focus {
                  outline: none;
                  border: none;
                  box-shadow: none;
                }
              }
            }
          }
        }
      }
    }
  }

  @media (min-width: 769px) and (max-width: 1200px) {
    .header-menu {
      .el-menu--horizontal {
        gap: 1rem; // Adjust gap as needed for tablet view
        padding: 0 10px;

        .action {
          display: flex;
          align-items: center;
          gap: 0.5rem; // Reduce the gap between action items
          flex: 1;
        }

        .form {
          flex: 2; // Make the search bar take more space
          display: flex;
          align-items: center;
          justify-content: flex-end; // Align search bar to the right
        }
      }
    }
  }

  @media (max-width: 1200px) {
    .el-header {
      .el-menu--horizontal {
        gap: 7rem;
      }
    }
  }
}
</style>
