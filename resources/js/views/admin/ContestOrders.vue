<template>
  <h1>Meus pedidos</h1>
</template>

<script lang="ts">
import { defineComponent } from "@vue/runtime-core";

export default defineComponent({
  name: "AdminContestOrders",
});
// import debounce from "lodash.debounce";
// import moment from "moment";
// import { moneyFormat } from "@/utils/moneyFormat";

// import { listContestOrders } from "@/services/contests";
// import { adminPaidNumbers, adminCancelOrder } from "@/services/numbers";

// export default {
//   name: "AdminContestOrders",
//   data() {
//     return {
//       loading: false,
//       params: {
//         search: "",
//         page: 1,
//       },
//       pager: {
//         current_page: 1,
//         first_page: 1,
//         last_page: 1,
//         per_page: 10,
//         from: 1,
//         to: 10,
//         total: 10,
//       },
//       fields: [
//         {
//           key: "customer",
//           sortable: true,
//           label: "Cliente",
//         },
//         {
//           key: "whatsapp",
//           sortable: false,
//           label: "WhatsApp",
//         },
//         {
//           key: "total",
//           sortable: true,
//           label: "R$ Total",
//         },
//         {
//           key: "numbers",
//           sortable: false,
//           label: "Números",
//         },
//         {
//           key: "created_at",
//           sortable: true,
//           label: "Data do pedido",
//         },
//         {
//           key: "actions",
//           sortable: false,
//           label: "Ações",
//         },
//       ],
//       items: [],
//       order: {},
//     };
//   },
//   computed: {
//     orderNumbers() {
//       return this.order && this.order.numbers
//         ? this.order.numbers.join(", ")
//         : "";
//     },
//     orderName() {
//       return this.order && this.order.user ? this.order.user.name : "";
//     },
//     orderWhatsApp() {
//       return this.order && this.order.user ? this.order.user.whatsapp : "";
//     },
//   },
//   mounted() {
//     this.getContestOrders();
//   },
//   methods: {
//     async getContestOrders() {
//       try {
//         this.loading = true;
//         const { id } = this.$route.params;

//         const result = await listContestOrders(id, this.params);

//         this.items = result.data;
//         this.pager = {
//           current_page: result.current_page,
//           from: result.from,
//           last_page: result.last_page,
//           per_page: result.per_page,
//           to: result.to,
//           total: result.total,
//         };
//       } catch (error) {
//         this.items = [];
//       } finally {
//         this.loading = false;
//       }
//     },
//     handleSearch: debounce(async function (e) {
//       const { value } = e.target;

//       this.params.search = value;

//       await this.getContestOrders();
//     }, 500),
//     async handlePaginate(page) {
//       this.params.page = page;
//       await this.getContestOrders();
//     },
//     openConfirmationModal(item) {
//       // this.$bvModal.show("confirmation-payment");
//       this.order = { ...item, numbers: JSON.parse(item.numbers) };
//     },
//     closeConfirmationModal() {
//       // this.$bvModal.hide("confirmation-payment");
//       this.order = {};
//     },
//     openCancelOrderModal(item) {
//       // this.$bvModal.show("cancel-order-modal");
//       this.order = { ...item, numbers: JSON.parse(item.numbers) };
//     },
//     closeCancelOrderModal() {
//       // this.$bvModal.hide("cancel-order-modal");
//       this.order = {};
//     },
//     async handleConfirmationPayment() {
//       try {
//         const { id } = this.$route.params;

//         this.loading = true;

//         const result = await adminPaidNumbers(id, {
//           customer_id: this.order.user.id,
//         });

//         this.getContestOrders();

//         console.log(result);
//       } catch (error) {
//         console.error(error);
//       } finally {
//         this.loading = false;
//       }
//     },
//     async handleCancelOrder() {
//       try {
//         this.loading = true;

//         const result = await adminCancelOrder(this.order.id);

//         this.getContestOrders();

//         console.log(result);
//       } catch (error) {
//         console.error(error);
//       } finally {
//         this.loading = false;
//       }
//     },
//     formatMoney(value) {
//       return moneyFormat(value);
//     },
//     formatDate(date) {
//       return moment(date).format("DD/MM/YYYY");
//     },
//     contestNumbers(item, split) {
//       const numbers = JSON.parse(item.numbers);

//       return numbers.splice(0, split || numbers.length).join(", ");
//     },
//     whatsAppLink(item) {
//       const { name, whatsapp } = item.user;
//       const message = `Olá ${name}, somos da equipe do Presente Sincero, tudo bem?`;
//       const link = `https://wa.me/55${whatsapp}?text=${encodeURI(message)}`;

//       return link;
//     },
//   },
// };
</script>

<style>
</style>