<template>
  <h1>AdminCustomerList</h1>
  <!-- <div>
    <div>
      <h2>Clientes</h2>
    </div>

    <div class="my-4 d-flex justify-content-between">
      <b-form-input
        v-model="params.name"
        placeholder="Pesquisar cliente"
      ></b-form-input>
    </div>

    <div class="table-responsive">
      <b-table
        id="customer-list"
        class="text-white"
        :fields="fields"
        :items="items"
        :per-page="params.perPage"
        :current-page="params.currentPage"
        :busy.sync="loading"
      >
        <template v-slot:head()="scope">
          <div style="width: 150px">{{ scope.label }}</div>
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
  </div> -->
</template>

<script>
export default {
  name: "AdminCustomerList",
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
          key: "name",
          sortable: true,
          label: "Cliente",
        },
        {
          key: "whatsapp",
          sortable: false,
          label: "WhatsApp",
        },
        {
          key: "contests_participated",
          sortable: true,
          label: "Participou",
        },
        {
          key: "contest_won",
          sortable: true,
          label: "Ganhou",
        },
        {
          key: "value_bet",
          sortable: true,
          label: "Valor investido",
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
  methods: {
    async handlePaginate(page) { },
  },
};
</script>

<style>
</style>