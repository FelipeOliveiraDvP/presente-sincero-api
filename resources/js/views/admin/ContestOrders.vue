<template>
  <b-container fluid>
    <h2>Pedidos</h2>

    <div class="my-4 d-flex justify-content-end">
      <input
        class="form-control"
        v-model="params.search"
        placeholder="Pesquisar cliente por nome, WhatsApp ou e-mail"
        @input="handleSearch"
      />
    </div>

    <div class="table-responsive">
      <b-table
        id="contest-list"
        class="text-white"
        :fields="fields"
        :items="items"
        :busy.sync="loading"
      >
        <template v-slot:head()="scope">
          <div style="width: 150px">{{ scope.label }}</div>
        </template>

        <template #cell(customer)="data">
          {{ data.item.user.name }}
        </template>

        <template #cell(whatsapp)="data">
          {{ data.item.user.whatsapp }}
          <a
            :href="whatsAppLink(data.item)"
            target="_blank"
            rel="noopener noreferrer"
          >
            <b-button
              variant="success"
              class="rounded-circle ml-1"
              :title="`Conversar com ${data.item.user.name}`"
              v-b-tooltip.hover.top
            >
              <font-awesome-icon icon="fa-brands fa-whatsapp" />
            </b-button>
          </a>
        </template>

        <template #cell(total)="data">
          {{ formatMoney(data.item.total) }}
        </template>

        <template #cell(numbers)="data">
          <span :title="contestNumbers(data.item)" v-b-tooltip.hover.bottom>
            {{ contestNumbers(data.item, 10) }}
          </span>
        </template>

        <template #cell(created_at)="data">
          {{ formatDate(data.item.created_at) }}
        </template>

        <template #cell(actions)="data">
          <b-button
            variant="primary"
            title="Marcar como pago"
            @click="openConfirmationModal(data.item)"
            v-b-tooltip.hover.top
          >
            <i class="fa-solid fa-money-bill-1"></i>
          </b-button>

          <b-button
            variant="danger"
            title="Cancelar pedido"
            v-b-tooltip.hover.top
            @click="openCancelOrderModal(data.item)"
          >
            <i class="fa-regular fa-trash-can"></i>
          </b-button>
        </template>
      </b-table>
    </div>

    <b-pagination
      :v-model="params.page"
      :per-page="pager.per_page"
      :total-rows="pager.total"
      pills
      align="end"
      @change="handlePaginate"
    ></b-pagination>

    <!-- Modal confirmar pagamento dos números -->
    <b-modal
      id="confirmation-payment"
      ok-variant="danger"
      ok-title="Sim, confirmar o pagamento"
      cancel-title="Não"
      @ok="handleConfirmationPayment"
      @hide="closeConfirmationModal"
    >
      <template #modal-title> Confirmar pagamento </template>
      <div>
        <p>
          Ao clicar no botão sim, você confirma o pagamento dos números abaixo:
        </p>
        <p class="text-bold">
          {{ orderNumbers }}
        </p>
        <p>
          Para o cliente
          <strong>{{ orderName }}</strong> WhatsApp
          <strong>{{ orderWhatsApp }}</strong>
        </p>
        <p><strong>Deseja confirmar o pagamento?</strong></p>
      </div>
    </b-modal>

    <!-- Modal confirmar liberação de números -->
    <b-modal
      id="cancel-order-modal"
      ok-variant="danger"
      ok-title="Sim, desejo cancelar o pedido"
      cancel-title="Não"
      @ok="handleCancelOrder"
    >
      <template #modal-title> Cancelar pedido </template>
      <div>
        <p>
          Ao clicar no botão sim, o pedido será cancelado e os números vão ser
          disponibilizados novamente.
        </p>
        <p><strong>Deseja realmente cancelar o pedido?</strong></p>
      </div>
    </b-modal>
  </b-container>
</template>

<script>
import debounce from "lodash.debounce";
import moment from "moment";
import moneyFormat from "@/utils/moneyFormat";

import { listContestOrders } from "@/services/contests";
import { adminPaidNumbers, adminCancelOrder } from "@/services/numbers";

export default {
  name: "AdminContestOrders",
  data() {
    return {
      loading: false,
      params: {
        search: "",
        page: 1,
      },
      pager: {
        current_page: 1,
        first_page: 1,
        last_page: 1,
        per_page: 10,
        from: 1,
        to: 10,
        total: 10,
      },
      fields: [
        {
          key: "customer",
          sortable: true,
          label: "Cliente",
        },
        {
          key: "whatsapp",
          sortable: false,
          label: "WhatsApp",
        },
        {
          key: "total",
          sortable: true,
          label: "R$ Total",
        },
        {
          key: "numbers",
          sortable: false,
          label: "Números",
        },
        {
          key: "created_at",
          sortable: true,
          label: "Data do pedido",
        },
        {
          key: "actions",
          sortable: false,
          label: "Ações",
        },
      ],
      items: [],
      order: {},
    };
  },
  computed: {
    orderNumbers() {
      return this.order && this.order.numbers
        ? this.order.numbers.join(", ")
        : "";
    },
    orderName() {
      return this.order && this.order.user ? this.order.user.name : "";
    },
    orderWhatsApp() {
      return this.order && this.order.user ? this.order.user.whatsapp : "";
    },
  },
  mounted() {
    this.getContestOrders();
  },
  methods: {
    async getContestOrders() {
      try {
        this.loading = true;
        const { id } = this.$route.params;

        const result = await listContestOrders(id, this.params);

        this.items = result.data;
        this.pager = {
          current_page: result.current_page,
          from: result.from,
          last_page: result.last_page,
          per_page: result.per_page,
          to: result.to,
          total: result.total,
        };
      } catch (error) {
        this.items = [];
      } finally {
        this.loading = false;
      }
    },
    handleSearch: debounce(async function (e) {
      const { value } = e.target;

      this.params.search = value;

      await this.getContestOrders();
    }, 500),
    async handlePaginate(page) {
      this.params.page = page;
      await this.getContestOrders();
    },
    openConfirmationModal(item) {
      this.$bvModal.show("confirmation-payment");
      this.order = { ...item, numbers: JSON.parse(item.numbers) };
    },
    closeConfirmationModal() {
      this.$bvModal.hide("confirmation-payment");
      this.order = {};
    },
    openCancelOrderModal(item) {
      this.$bvModal.show("cancel-order-modal");
      this.order = { ...item, numbers: JSON.parse(item.numbers) };
    },
    closeCancelOrderModal() {
      this.$bvModal.hide("cancel-order-modal");
      this.order = {};
    },
    async handleConfirmationPayment() {
      try {
        const { id } = this.$route.params;

        this.loading = true;

        const result = await adminPaidNumbers(id, {
          customer_id: this.order.user.id,
        });

        this.getContestOrders();

        this.$toasted.show(result.message, {
          type: "success",
          theme: "toasted-primary",
          position: "top-right",
          duration: 3000,
        });
      } catch (error) {
        this.$toasted.show(error.message, {
          type: "error",
          theme: "toasted-primary",
          position: "top-right",
          duration: 3000,
        });
      } finally {
        this.loading = false;
      }
    },
    async handleCancelOrder() {
      try {
        this.loading = true;

        const result = await adminCancelOrder(this.order.id);

        this.getContestOrders();

        this.$toasted.show(result.message, {
          type: "success",
          theme: "toasted-primary",
          position: "top-right",
          duration: 3000,
        });
      } catch (error) {
        this.$toasted.show(error.message, {
          type: "error",
          theme: "toasted-primary",
          position: "top-right",
          duration: 3000,
        });
      } finally {
        this.loading = false;
      }
    },
    formatMoney(value) {
      return moneyFormat(value);
    },
    formatDate(date) {
      return moment(date).format("DD/MM/YYYY");
    },
    contestNumbers(item, split) {
      const numbers = JSON.parse(item.numbers);

      return numbers.splice(0, split || numbers.length).join(", ");
    },
    whatsAppLink(item) {
      const { name, whatsapp } = item.user;
      const message = `Olá ${name}, somos da equipe do Presente Sincero, tudo bem?`;
      const link = `https://wa.me/55${whatsapp}?text=${encodeURI(message)}`;

      return link;
    },
  },
};
</script>

<style>
</style>