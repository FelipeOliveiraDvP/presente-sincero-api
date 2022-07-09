<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2>Sorteios</h2>
      <router-link class="btn btn-primary" to="/admin/sorteios/novo-sorteio">
        Novo sorteio
      </router-link>
    </div>

    <b-row>
      <b-col sm="12" md="6" lg="4" class="mb-3">
        <b-form-datepicker
          placeholder="Pesquisar por data"
          v-model="params.date"
        ></b-form-datepicker>
      </b-col>

      <b-col sm="12" md="6" lg="4">
        <b-form-input
          v-model="params.title"
          placeholder="Pesquisar sorteio"
        ></b-form-input>
      </b-col>
    </b-row>

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

        <template #cell(price)="data">
          {{ formatMoney(data.item.price) }}
        </template>

        <template #cell(paid_percentage)="data">
          {{ `${data.item.paid_percentage * 100}%` }}
        </template>

        <template #cell(actions)="data">
          <router-link :to="`/admin/sorteios/${data.item.id}`">
            <b-button variant="primary">
              <i class="fas fa-solid fa-pen"></i>
            </b-button>
          </router-link>
          <router-link :to="`/admin/sorteios/${data.item.id}/gerenciar`">
            <b-button variant="primary">
              <font-awesome-icon :icon="['fas', 'gear']" class="icon alt" />
            </b-button>
          </router-link>
          <router-link :to="`/admin/sorteios/${data.item.id}/pedidos`">
            <b-button variant="primary">
              <i class="fa-solid fa-file-invoice-dollar"></i>
            </b-button>
          </router-link>
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
  </div>
</template>

<script>
import moneyFormat from "@/utils/moneyFormat";
import { listContestsByUser } from "@/services/contests";

export default {
  name: "AdminContestList",
  data() {
    return {
      loading: false,
      params: {
        title: "",
        limit: 10,
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
          key: "title",
          sortable: true,
          label: "Sorteio",
        },
        {
          key: "price",
          sortable: true,
          label: "R$ Valor",
        },
        {
          key: "quantity",
          sortable: true,
          label: "Quantidade",
        },
        {
          key: "paid_percentage",
          sortable: true,
          label: "% vendido",
        },
        {
          key: "actions",
          sortable: false,
          label: "Ações",
        },
      ],
      items: [],
    };
  },
  computed: {},
  mounted() {
    this.getContests();
  },
  methods: {
    async getContests() {
      try {
        this.loading = true;

        const result = await listContestsByUser(this.params);

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
    async handlePaginate(page) {
      this.params.page = page;
      await this.getContests();
    },
    formatMoney(value) {
      return moneyFormat(value);
    },
  },
};
</script>

<style>
</style>