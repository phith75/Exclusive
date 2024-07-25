<template>
    <aside :class="{ collapsed: isSidebarCollapsed }">
      <div class="logo">
        <router-link :to="{ name: 'dashboard' }">
          <img src="@/assets/images/logo.png" alt="Logo" />
        </router-link>
      </div>
      <nav>
        <ul class="menu" id="menu">
          <li v-for="menuItem in menuItems" :key="menuItem.path" :class="{ active: activeMenuItem === menuItem.path }">
            <router-link :to="{ name: menuItem.path }" @click="setActiveMenuItem(menuItem.path)">
              <div class="btn">
                <img :src="`../src/assets/images/${menuItem.icon}`" :alt="menuItem.label" />
                <span>{{ menuItem.label }}</span>
              </div>
            </router-link>
          </li>
        </ul>
      </nav>
    </aside>
  </template>
  
  <script setup lang="ts" >
  import { ref } from 'vue';
  import { useSidebar } from '@/composables/useSidebar';
  
  const { isSidebarCollapsed, activeMenuItem, setActiveMenuItem } = useSidebar();
  
  const arrayMenu = {
    'Dashboard': 'dashboard',
    'List user': 'user.list',
  };
  
  // Convert arrayMenu to an array of objects
  const menuItems = ref(
    Object.entries(arrayMenu).map(([label, path]) => ({
      label,
      path,
      icon: 'icon-2.png',
    }))
  );
  </script>
  
  <style scoped>
  a {
    text-decoration: none;
    color: inherit;
  }
  
  aside {
    transition: width 0.6s;
  }
  
  
  aside.collapsed {
    width: 6.5%;
  }
  
  aside.collapsed .btn {
    transition: 0.6s;
    justify-content: center;
    width: 50%;
  }
  aside.collapsed li .btn{
    margin: 0 auto;
  }
  
  aside.collapsed .logo img {
    transition: width 0.6s;
    justify-content: center;
    width: 70%;
  }
  
  aside.collapsed .btn span {
    width: 0;
    display: none;
  }
  .menu li{
   margin: 0px 10px 0 20px ;
   border-radius: 7px;
  }
  
  .menu li.active {
    color: #fff;
    background-color: #4880FF;
  }
  
  .menu li.active::before {
    height: 50px;
    width: 8px;
    content: "";
    left: 0;
    position: absolute;
    background-color: #4880FF;
    border-bottom-right-radius: 5px;
    border-top-right-radius: 5px;
  }
  
  .menu li .btn {
    display: flex;
    align-items: center;
  }
  
  </style>
  