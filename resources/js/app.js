require("./bootstrap");

import Vue from "vue";

import VueClipboard from "vue-clipboard2";

import App from "./App.vue";
import router from "./routes";
import store from "./store";

Vue.use(VueClipboard);

const app = new Vue({
  el: "#app",
  router: router,
  store: store,
  render: (h) => h(App),
  created() {
    window.Echo.channel("payment.confirmed").listen(
      "PaymentConfirmed",
      async (e) => {
        const payment = {
          userId: e.user_id,
          orderId: e.order_id,
          confirmed: e.confirmed,
        };

        await this.$store.dispatch("payment/confirmPayment", payment);
      }
    );
  },
});
