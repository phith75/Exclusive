// src/router/index.js
import { createRouter, createWebHistory } from "vue-router";
import routes from "@/router/router";

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(_to, _from, savePosition) {
    if (savePosition) {
      return savePosition;
    } else {
      return { top: 0 };
    }
  },
});

router.beforeEach((to, _from, next) => {
  const isAuthenticated = localStorage.getItem("token");
  const exceptAuth = ["Login"];
  if (!isAuthenticated && !exceptAuth.includes(to.name as string)) {
    next({ name: "Login" });
  } else {
    next();
  }
});
export default router;
