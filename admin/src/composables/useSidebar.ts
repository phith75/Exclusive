import { ref, watch } from "vue";
import { useRoute } from "vue-router";

const isSidebarCollapsed = ref(false);
const activeMenuItem = ref(null);

export function useSidebar() {
  const route = useRoute();

  // Watch for route path changes to set the active menu item
  watch(
    () => route.path,
    (newPath) => {
      const basePath = newPath.split("/").slice(0, 3).join("/");
      activeMenuItem.value = basePath;
    },
    { immediate: true }
  );

  return {
    isSidebarCollapsed,
    activeMenuItem,
    toggleSidebar() {
      isSidebarCollapsed.value = !isSidebarCollapsed.value;
    },
    setActiveMenuItem(menuItem: any) {
      activeMenuItem.value = menuItem;
    },
  };
}
