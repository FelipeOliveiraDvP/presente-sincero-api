import Vue from "vue";
import { BootstrapVue, IconsPlugin } from "bootstrap-vue";
import VueFormulate from "@braid/vue-formulate";
import Toasted from "vue-toasted";
import VueTheMask from "vue-the-mask";

import App from "./App.vue";
import router from "./routes";

import "bootstrap-vue/dist/bootstrap-vue.css";
import "bootstrap-vue/dist/bootstrap-vue-icons.min.css";
import "@fortawesome/fontawesome-free/css/all.css";
import "@fortawesome/fontawesome-free/js/all.js";
import "animate.css";

Vue.use(BootstrapVue);
Vue.use(IconsPlugin);
Vue.use(VueFormulate);
Vue.use(Toasted, Option);
Vue.use(VueTheMask);

const app = new Vue({
    el: "#app",
    router: router,
    render: (h) => h(App),
});
