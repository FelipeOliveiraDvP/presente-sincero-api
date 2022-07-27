require("./bootstrap");

import { createApp, h } from "vue";
import { createPinia } from "pinia";
import piniaPersist from "pinia-plugin-persist";
import VueClipboard from "vue-clipboard2";
import Antd from "ant-design-vue";

import router from "./routes";
import App from "./App.vue";

import "ant-design-vue/dist/antd.css";

const app = createApp({
  render: () => h(App),
});

const pinia = createPinia();
pinia.use(piniaPersist);

app.use(router);
app.use(pinia);
app.use(VueClipboard);
app.use(Antd);

app.mount("#app");
