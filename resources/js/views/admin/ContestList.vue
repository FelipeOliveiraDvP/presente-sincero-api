<template>
  <div>
    <div class="d-flex justify-content-between align-items-center">
      <h2>Sorteios</h2>
      <router-link class="btn btn-primary" to="/admin/sorteios/novo-sorteio">
        Novo sorteio
      </router-link>
    </div>

    <div class="my-4 d-flex justify-content-between">
      <div>
        <b-form-datepicker
          placeholder="Pesquisar por data"
          v-model="params.date"
          style="width: 300px"
        ></b-form-datepicker>
      </div>

      <b-form-input
        v-model="params.title"
        placeholder="Pesquisar sorteio"
        style="width: 300px"
      ></b-form-input>
    </div>

    <b-table
      id="contest-list"
      striped
      hover
      :fields="fields"
      :items="items"
      :busy.sync="loading"
    >
      <template #cell(start_date)="data">
        {{ formatDate(data.item.start_date) }}
      </template>

      <template #cell(contest_date)="data">
        {{ formatDate(data.item.contest_date) }}
      </template>

      <template #cell(actions)="data">
        <router-link :to="`/admin/sorteios/${data.item.id}`">
          Ver mais
        </router-link>
      </template>
    </b-table>

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
import moment from "moment";
import { listContests } from "../../services/contests";

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
          label: "Nome",
        },
        {
          key: "start_date",
          sortable: true,
          label: "Data de início",
        },
        {
          key: "days_to_end",
          sortable: true,
          label: "Dias para terminar",
        },
        {
          key: "contest_date",
          sortable: true,
          label: "Data do sorteio",
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

        const result = await listContests(this.params);

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
    formatDate(date) {
      return moment(date).format("DD/MM/YYYY");
    },
  },
};
</script>

<style>
</style>