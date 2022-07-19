<template>
  <div class="site">
    <router-view />
  </div>
</template>

<script>
export default {
  name: "App",
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
  }
};
</script>

<style>
</style>