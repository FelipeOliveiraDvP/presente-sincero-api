<template>
  <a-page-header title="Pedidos" />

  <a-row>
    <a-col
      :xs="24"
      :md="{ span: 12, offset: 12 }"
      :lg="{ span: 8, offset: 16 }"
    >
      <a-input-search
        v-model:value="filters.search"
        placeholder="Pesquisar pedidos por nome ou WhatsApp"
        enter-button
        @input="handleSearch"
      />
    </a-col>
  </a-row>

  <a-table
    :columns="columns"
    :row-key="(record) => record.id"
    :data-source="orders"
    :scroll="{ x: 950 }"
    :loading="loading"
    :pagination="false"
  >
    <template #bodyCell="{ column, record }">
      <template v-if="column.key === 'id'"> #{{ record.id }} </template>

      <template v-if="column.key === 'total'">
        {{ formatPrice(record.total) }}
      </template>

      <template v-if="column.key === 'name'">
        {{ record.user.name }}
      </template>

      <template v-if="column.key === 'whatsapp'">
        <a-typography-link :href="whatsappLink(record)" target="_blank">
          {{ maskCellphone(record.user.whatsapp) }}
          <whats-app-outlined />
        </a-typography-link>
      </template>

      <template v-if="column.key === 'status'">
        <a-tag color="warning">
          <template #icon>
            <exclamation-circle-outlined />
          </template>
          Pendente
        </a-tag>
      </template>

      <template v-if="column.key === 'created_at'">
        {{ formatDate(record.created_at) }}
      </template>

      <template v-if="column.key === 'actions'">
        <a-space>
          <a-button type="primary" @click="toggleApproveModal(record)">
            <template #icon><check-outlined /></template>
            Aprovar
          </a-button>

          <a-button type="primary" danger @click="toggleCancelModal(record)">
            <template #icon>
              <stop-outlined />
            </template>
            Cancelar
          </a-button>
        </a-space>
      </template>
    </template>
  </a-table>

  <pagination :pager="pager" @paginate="handlePaginate" />

  <approve-order-modal
    :order="selectedOrder"
    :visible="showApproveModal"
    :loading="loading"
    @approve="handleApproveOrder"
    @cancel="toggleApproveModal"
  />

  <cancel-order-modal
    :order="selectedOrder"
    :visible="showCancelModal"
    :loading="loading"
    @approve="handleCancelOrder"
    @cancel="toggleCancelModal"
  />
</template>

<script lang="ts">
import { ContestOrderItem } from "@/types/Contest.types";
import { defineComponent, onMounted, reactive, ref } from "@vue/runtime-core";
import { ColumnsType } from "ant-design-vue/lib/vc-table/interface";
import {
  WhatsAppOutlined,
  StopOutlined,
  CheckOutlined,
  ExclamationCircleOutlined,
} from "@ant-design/icons-vue";
import { BaseQuery, PaginatedResponse } from "@/types/api.types";
import { listContestOrders } from "@/services/contests";
import { useRoute } from "vue-router";
import { debounce } from "lodash";
import { ChangeEvent } from "ant-design-vue/lib/_util/EventInterface";
import * as moment from "moment";
import { cellphoneMask } from "@/utils/cellphoneMask";
import { adminCancelOrder, adminPaidOrder } from "@/services/numbers";
import { notification } from "ant-design-vue";
import { getErrorMessage } from "@/utils/handleError";
import { moneyFormat } from "@/utils/moneyFormat";
import Pagination from "@/components/_commons/Pagination.vue";
import ApproveOrderModal from "@/components/Contests/Admin/ApproveOrderModal.vue";
import CancelOrderModal from "@/components/Contests/Admin/CancelOrderModal.vue";

const columns: ColumnsType<ContestOrderItem> = [
  {
    title: "Nº do pedido",
    dataIndex: "id",
    key: "id",
    width: 100,
  },
  {
    title: "R$ Total",
    dataIndex: "total",
    key: "total",
    width: 150,
  },
  {
    title: "Cliente",
    dataIndex: "user.name",
    key: "name",
    width: 250,
  },
  {
    title: "WhatsApp",
    dataIndex: "user.whatsapp",
    key: "whatsapp",
    width: 250,
  },
  {
    title: "Status",
    dataIndex: "status",
    key: "status",
    width: 150,
  },
  {
    title: "Data do pedido",
    dataIndex: "created_at",
    key: "created_at",
    width: 150,
  },
  {
    title: "Ações",
    dataIndex: "actions",
    key: "actions",
    width: 150,
  },
];

export default defineComponent({
  name: "AdminContestOrders",
  components: {
    WhatsAppOutlined,
    StopOutlined,
    CheckOutlined,
    ExclamationCircleOutlined,
    Pagination,
    ApproveOrderModal,
    CancelOrderModal,
  },
  setup() {
    const route = useRoute();
    const { id } = route.params;
    const loading = ref<boolean>(false);
    const orders = ref<ContestOrderItem[]>();
    const selectedOrder = ref<ContestOrderItem>();
    const showApproveModal = ref<boolean>(false);
    const showCancelModal = ref<boolean>(false);

    const filters = reactive<BaseQuery>({
      search: "",
      page: 1,
    });

    const pager = ref<PaginatedResponse<ContestOrderItem>>({
      current_page: 1,
      total: 1,
      per_page: 1,
    });

    async function getOrders(params: BaseQuery) {
      loading.value = true;

      const response = await listContestOrders(Number(id), params);

      pager.value.current_page = response.current_page || 1;
      pager.value.total = response.total || 1;
      pager.value.per_page = response.per_page || 1;
      orders.value = response.data;
      loading.value = false;
    }

    async function handlePaginate(page: number) {
      filters.page = page;

      await getOrders(filters);
    }

    const handleSearch = debounce(async (e: ChangeEvent) => {
      filters.search = e.target.value;

      await getOrders(filters);
    }, 500);

    async function handleApproveOrder(order?: ContestOrderItem) {
      if (order === undefined) return;

      try {
        loading.value = false;
        toggleApproveModal(undefined);

        const result = await adminPaidOrder(Number(id), order.id);

        notification.success({
          message: result.message,
        });

        await getOrders(filters);
      } catch (error: unknown) {
        notification.error({
          message: getErrorMessage(error),
        });
      } finally {
        loading.value = false;
      }
    }

    async function handleCancelOrder(order?: ContestOrderItem) {
      if (order === undefined) return;

      try {
        loading.value = false;
        toggleCancelModal(undefined);

        const result = await adminCancelOrder(Number(id), order.id);

        notification.success({
          message: result.message,
        });

        await getOrders(filters);
      } catch (error: unknown) {
        notification.error({
          message: getErrorMessage(error),
        });
      } finally {
        loading.value = false;
      }
    }

    function toggleApproveModal(order?: ContestOrderItem) {
      selectedOrder.value = order;
      showApproveModal.value = !showApproveModal.value;
    }

    function toggleCancelModal(order?: ContestOrderItem) {
      selectedOrder.value = order;
      showCancelModal.value = !showCancelModal.value;
    }

    function whatsappLink(order: ContestOrderItem) {
      const { whatsapp, name } = order.user;
      const message = `Olá ${name}, somos a equipe da Rifandos, tudo bem?`;

      return `https://api.whatsapp.com/send?phone=55${whatsapp}&text=${encodeURI(
        message
      )}`;
    }

    onMounted(async () => {
      await getOrders(filters);
    });

    return {
      columns,
      filters,
      orders,
      loading,
      pager,
      selectedOrder,
      showApproveModal,
      showCancelModal,

      handleSearch,
      handlePaginate,
      whatsappLink,
      toggleApproveModal,
      toggleCancelModal,
      handleApproveOrder,
      handleCancelOrder,
      formatPrice: (value: number) => moneyFormat(value),
      formatDate: (date: string) => moment(date).format("DD/MM/YYYY"),
      maskCellphone: (phone: string) => cellphoneMask(phone),
    };
  },
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