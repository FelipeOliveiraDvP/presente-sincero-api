<template>
  <div class="site">
    <router-view />
  </div>
</template>

<script lang="ts">
// import { defineComponent, onMounted } from "vue";
// import { usePaymentStore } from "./store/payment";

// export default defineComponent({
//   name: "App",
//   // setup() {
//   //   const { confirmPayment } = usePaymentStore();

//   //   onMounted(() => {
//   //     window.Echo.channel("payment.confirmed").listen(
//   //       "PaymentConfirmed",
//   //       (e) => {
//   //         const payment = {
//   //           userId: e.user_id,
//   //           orderId: e.order_id,
//   //           confirmed: e.confirmed,
//   //         };

//   //         confirmPayment(payment);
//   //       }
//   //     );
//   //   });
//   // },
// });
</script>

<style>
</style>