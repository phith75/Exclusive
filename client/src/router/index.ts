import { createRouter, createWebHistory } from "vue-router";
import routes from "./router.ts";
import { toast } from "vue3-toastify";

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
  const exceptAuth = [
    "Homepage",
    "Login",
    "Register",
    "NotFound",
    "Books",
    "BookDetail",
    "Cart",
    "Contact",
    "About",
    "GoogleCallback",
    "ForgotPassword",
    "ResetPassword",
  ];

  if (!isAuthenticated && !exceptAuth.includes(to.name as string)) {
    localStorage.setItem("redirectPath", to.fullPath);
    next({ name: "Login" });
    setTimeout(() => {
      toast.error("Please login first");
    }, 200);
  } else {
    next();
  }
});

export default router;
