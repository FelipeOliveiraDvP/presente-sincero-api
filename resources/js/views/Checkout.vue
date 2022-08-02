<template>
  <h1>Checkout</h1>
</template>

<script lang="ts">
import { defineComponent } from "@vue/runtime-core";

export default defineComponent({
  name: "Checkout",
});
// import ContestNumberVue from "@/components/Contests/ContestNumber.vue";
// import LoaderVue from "@/components/_commons/Loader.vue";
// import { moneyFormat } from "@/utils/moneyFormat";

// import { reserveNumbers, freeNumbers } from "@/services/numbers";

// export default {
//   name: "Checkout",
//   components: {
//     "my-loader": LoaderVue,
//     "contest-number": ContestNumberVue,
//   },
//   data() {
//     return {
//       loading: false,
//       leaving: false,
//       numbers: [],
//       total: 0,
//       details: {
//         slug: "",
//         short_description: "",
//         price: 0,
//       },
//       payment: {
//         order_id: null,
//         qrcode_base64: "",
//         qr_code: "",
//       },
//     };
//   },
//   created() {
//     const { numbers, total, details } = this.$route.params;

//     if (!numbers || !total || !details) {
//       this.$router.push({
//         name: "contests",
//       });
//       return;
//     }

//     this.numbers = numbers || [];
//     this.total = total || 0;
//     this.details = { ...details };
//     this.leaving = true;

//     window.addEventListener("beforeunload", this.showLeaveConfirmation);

//     this.getQRCode();
//   },
//   computed: {
//     paymentConfirmed() {
//       const { auth, payment } = this.$store.state;
//       const isCurrentUser = auth.user.id === payment.userId;
//       const isCurrentOrder = payment.orderId === this.payment.order_id;
//       const isConfirmed = payment.confirmed === true;

//       return isCurrentUser && isCurrentOrder && isConfirmed;
//     },
//   },
//   watch: {
//     paymentConfirmed(newVal, oldVal) {
//       if (newVal === true) {
//         this.leaving = false;
//       }
//     },
//   },
//   methods: {
//     async getQRCode() {
//       try {
//         this.loading = true;

//         const { contestId } = this.details;
//         const order = {
//           total: this.total,
//           numbers: this.numbers.map((n) => n.number),
//         };

//         const { payment } = await reserveNumbers(contestId, order);

//         this.payment = { ...payment };
//       } catch (error) {
//         console.error(error);

//         this.$router.push({
//           name: "contests",
//         });
//       } finally {
//         this.loading = false;
//       }
//     },
//     async freeReservedNumbers() {
//       try {
//         this.loading = true;

//         const { contestId } = this.details;
//         const order = {
//           numbers: this.numbers.map((n) => n.number),
//         };

//         await freeNumbers(contestId, order);

//         this.addMoreNumbers();
//       } catch (error) {
//         console.error(error);

//         this.$router.push({
//           name: "contests",
//         });
//       } finally {
//         this.loading = false;
//       }
//     },
//     showLeaveConfirmation(e) {
//       e.returnValue = "";
//     },
//     addMoreNumbers() {
//       this.leaving = false;
//       this.$router.push({
//         name: "contestDetail",
//         params: {
//           slug: this.details.slug,
//           checkout: {
//             totals: this.total,
//             numbers: this.numbers,
//           },
//         },
//       });
//     },
//     formatPrice(price) {
//       return moneyFormat(price);
//     },
//     onCopySuccess(e) {
//       this.$toasted.show("Código copiado com sucesso!", {
//         type: "success",
//         theme: "toasted-primary",
//         position: "top-right",
//         duration: 3000,
//       });
//     },
//     onCopyError(e) {
//       this.$toasted.show("Erro ao copiar o código", {
//         type: "error",
//         theme: "toasted-primary",
//         position: "top-right",
//         duration: 3000,
//       });
//     },
//   },
//   beforeRouteLeave(to, from, next) {
//     if (this.leaving) {
//       // this.$bvModal.show("confirmation-leave-modal");
//       return;
//     }

//     next();
//   },
//   beforeDestroy() {
//     window.removeEventListener("beforeunload", this.showLeaveConfirmation);
//   },
// };
</script>

<style>
</style>