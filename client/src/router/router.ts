import type { RouteRecordRaw } from "vue-router";
import ClientLayout from "@/components/layouts/ClientLayout.vue";
import Login from "@/views/LoginPage.vue";
import registerPage from "@/views/registerPage.vue";
import Homepage from "@/views/HomePage.vue";
import WishListPage from "@/views/WishListPage.vue";
import BookListPage from "@/views/BookListPage.vue";
import CartPage from "@/views/CartPage.vue";
import CheckoutPage from "@/views/CheckoutPage.vue";
import MyProfilePage from "@/views/MyProfilePage.vue";
import ChangePasswordPage from "@/views/ChangePasswordPage.vue";
import ContactPage from "@/views/ContactPage.vue";
import BookDetailPage from "@/views/BookDetailPage.vue";
import TicketPage from "@/views/TicketPage.vue";
import AboutPage from "@/views/AboutPage.vue";
import LoadingGoogle from "@/views/LoadingGoogle.vue";
import ForgotPasswordPage from "@/views/ForgotPasswordPage.vue";
import ResetPasswordPage from "@/views/ResetPasswordPage.vue";
import NotFound from "@/views/NotFound.vue";

const routes: RouteRecordRaw[] = [
  {
    path: "",
    component: ClientLayout,
    redirect: "/home",
    children: [
      {
        path: "/login",
        name: "Login",
        component: Login,
      },
      {
        path: "/register",
        name: "Register",
        component: registerPage,
      },
      {
        path: "/google/callback",
        name: "GoogleCallback",
        component: LoadingGoogle,
      },

      {
        path: "/:pathMatch(.*)*",
        name: "NotFound",
        component: NotFound,
      },
      {
        path: "/home",
        name: "Homepage",
        component: Homepage,
      },
      {
        path: "/wishlist",
        name: "Wishlist",
        component: WishListPage,
      },
      {
        path: "/books",
        name: "Books",
        component: BookListPage,
      },
      {
        path: "/book-detail/:id",
        name: "BookDetail",
        component: BookDetailPage,
      },
      {
        path: "/cart",
        name: "Cart",
        component: CartPage,
      },
      {
        path: "/checkout",
        name: "Checkout",
        component: CheckoutPage,
      },
      {
        path: "/ticket-page",
        name: "TicketPage",
        component: TicketPage,
      },
      {
        path: "/my-profile",
        name: "MyProfile",
        component: MyProfilePage,
      },
      {
        path: "/change-password",
        name: "ChangePassword",
        component: ChangePasswordPage,
      },
      {
        path: "/about",
        name: "About",
        component: AboutPage,
      },
      {
        path: "/contact",
        name: "Contact",
        component: ContactPage,
      },
      {
        path: "/forgot-password",
        name: "ForgotPassword",
        component: ForgotPasswordPage,
      },
      {
        path: "/reset-password",
        name: "ResetPassword",
        component: ResetPasswordPage,
      },
    ],
  },
];

export default routes;
