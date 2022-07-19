require("./bootstrap");

import { createApp, h } from "vue";
import VueClipboard from "vue-clipboard2";
import Antd from "ant-design-vue";

import router from "./routes";
import store from "./store";
import App from "./App.vue";

import "ant-design-vue/dist/antd.css";

const app = createApp({
  render: () => h(App),
});

app.use(router);
app.use(store);
app.use(VueClipboard);
app.use(Antd);

app.mount("#app");
