<template>
  <a-config-provider :locale="ptBR">
    <div class="site">
      <router-view />
    </div>
  </a-config-provider>
</template>

<script lang="ts">
import { defineComponent, onMounted } from "vue";
import ptBR from "ant-design-vue/es/locale/pt_BR";

import { usePaymentStore } from "./store/payment";

export default defineComponent({
  name: "App",
  setup() {
    const { confirmPayment } = usePaymentStore();

    onMounted(() => {
      window.Echo.channel("payment.confirmed").listen(
        "PaymentConfirmed",
        (e) => {
          const payment = {
            userId: e.user_id,
            orderId: e.order_id,
            confirmed: e.confirmed,
          };

          confirmPayment(payment);
        }
      );
    });

    return {
      ptBR,
    };
  },
});
</script>

<style>
.ant-layout-content {
  min-height: unset;
  height: auto;
}
.ant-input-number {
  width: 100%;
}
.ant-btn.ant-btn-primary {
  background: #44853c;
  border-color: #44853c;
}
.ant-btn.ant-btn-primary:hover {
  background: #5f8f59;
  border-color: #5f8f59;
}
</style>