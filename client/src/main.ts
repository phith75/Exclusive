import { createApp } from "vue";
import { createPinia } from "pinia";
import App from "./App.vue";
import router from "@/router";
import Vue3Toastify from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import "element-plus/dist/index.css";
import "./assets/css/style.scss";
import * as ElementPlusIconsVue from "@element-plus/icons-vue";
import ElementPlus from "element-plus";

const app = createApp(App);
const pinia = createPinia();

app.use(pinia);
app.use(ElementPlus);
for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
  app.component(key, component);
}
app.use(router);
app.use(Vue3Toastify);

app.mount("#app");
