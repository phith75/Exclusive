import { createApp } from "vue";
import { createPinia } from "pinia";
import App from "./App.vue";
import router from "@/router/index.js";
import Vue3Toastify from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import "element-plus/dist/index.css";
import "./assets/css/style.css";
import { QuillEditor } from "@vueup/vue-quill";
import "@vueup/vue-quill/dist/vue-quill.snow.css";

import ElementPlus from "element-plus";

const app = createApp(App);
const pinia = createPinia();

app.use(pinia);
app.use(router);
app.use(ElementPlus);
app.use(Vue3Toastify);
app.component("QuillEditor", QuillEditor);

app.mount("#app");
