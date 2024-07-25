import type { RouteRecordRaw } from "vue-router";

import Login from "@/views/LoginForm.vue";
import AdminView from "@/views/AdminView.vue";
import Dashboard from "@/views/DashboardView.vue";

import AuthorList from "@/views/author/AuthorListView.vue";
import AuthorCreate from "@/views/author/AuthorCreateView.vue";
import AuthorUpdate from "@/views/author/AuthorUpdateView.vue";
import AuthorRestore from "@/views/author/AuthorTrashedView.vue";

import PublisherList from "@/views/publisher/PublisherListView.vue";
import PublisherCreate from "@/views/publisher/PublisherCreateView.vue";
import PublisherUpdate from "@/views/publisher/PublisherUpdateView.vue";
import PublisherRestore from "@/views/publisher/PublisherTrashedView.vue";

import CategoryList from "@/views/category/CategoryListView.vue";
import CategoryCreate from "@/views/category/CategoryCreateView.vue";
import CategoryUpdate from "@/views/category/CategoryUpdateView.vue";
import CategoryRestore from "@/views/category/CategoryTrashedView.vue";

import BookList from "@/views/book/BookListView.vue";
import BookCreate from "@/views/book/BookCreateView.vue";
import BookUpdate from "@/views/book/BookUpdateView.vue";
import BookRestore from "@/views/book/BookTrashedView.vue";

import UserList from "@/views/user/UserListView.vue";
import UserUpload from "@/views/user/UserUploadView.vue";

import LendTicketList from "@/views/ticket/TicketListView.vue";
import TicketDetail from "@/views/ticket/TicketDetailView.vue";

import Review from "@/views/review/ReviewListView.vue";
import ReviewRestore from "@/views/review/ReviewTrashedView.vue";

const authorRoutes = [
  { path: "author", name: "author.list", component: AuthorList },
  { path: "author/create", name: "author.create", component: AuthorCreate },
  { path: "author/update/:id", name: "author.update", component: AuthorUpdate },
  {
    path: "author/restore",
    name: "author.restore",
    component: AuthorRestore,
  },
];

const publisherRoutes = [
  { path: "publisher", name: "publisher.list", component: PublisherList },
  {
    path: "publisher/create",
    name: "publisher.create",
    component: PublisherCreate,
  },
  {
    path: "publisher/update/:id",
    name: "publisher.update",
    component: PublisherUpdate,
  },
  {
    path: "publisher/restore",
    name: "publisher.restore",
    component: PublisherRestore,
  },
];

const categoryRoutes = [
  { path: "category", name: "category.list", component: CategoryList },
  {
    path: "category/create",
    name: "category.create",
    component: CategoryCreate,
  },
  {
    path: "category/update/:id",
    name: "category.update",
    component: CategoryUpdate,
  },
  {
    path: "category/restore",
    name: "category.restore",
    component: CategoryRestore,
  },
];

const bookRoutes = [
  { path: "book", name: "book.list", component: BookList },
  {
    path: "book/create",
    name: "book.create",
    component: BookCreate,
  },
  {
    path: "book/update/:id",
    name: "book.update",
    component: BookUpdate,
  },
  {
    path: "book/restore",
    name: "book.restore",
    component: BookRestore,
  },
];

const userRoutes = [
  { path: "user", name: "user.list", component: UserList },
  { path: "user/upload", name: "user.upload", component: UserUpload },
];

const ticketRoutes = [
  { path: "ticket", name: "ticket.list", component: LendTicketList },
  { path: "ticket/detail/:id", name: "ticket.detail", component: TicketDetail },
];

const reviewRoutes = [
  { path: "review", name: "review.list", component: Review },
  { path: "review/trashed", name: "review.trashed", component: ReviewRestore },
];

const routes: RouteRecordRaw[] = [
  {
    path: "/login",
    name: "Login",
    component: Login,
  },
  {
    path: "/",
    redirect: "/login",
  },
  {
    path: "/admin",
    name: "Admin",
    component: AdminView,
    children: [
      {
        path: "dashboard",
        name: "dashboard",
        component: Dashboard,
      },
      ...authorRoutes,
      ...publisherRoutes,
      ...categoryRoutes,
      ...bookRoutes,
      ...userRoutes,
      ...ticketRoutes,
      ...reviewRoutes,
    ],
  },
];

export default routes;
