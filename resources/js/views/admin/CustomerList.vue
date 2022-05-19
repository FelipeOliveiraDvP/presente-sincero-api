<template>
  <div>
    <div>
      <h2>Clientes</h2>
    </div>

    <div class="my-4 d-flex justify-content-between">
      <div></div>

      <b-form-input
        v-model="params.name"
        placeholder="Pesquisar cliente"
        style="width: 300px"
      ></b-form-input>
    </div>

    <b-table
      id="customer-list"
      striped
      hover
      :fields="fields"
      :items="items"
      :per-page="params.perPage"
      :current-page="params.currentPage"
    ></b-table>

    <b-pagination
      v-model="params.currentPage"
      :total-rows="params.total"
      :per-page="params.perPage"
      aria-controls="customer-list"
    ></b-pagination>
  </div>
</template>

<script>
export default {
  name: "AdminCustomerList",
  data() {
    return {
      params: {
        name: "",
        total: 10,
        currentPage: 1,
        perPage: 5,
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
        // {
        //   key: "whatsapp",
        //   sortable: false,
        //   label: "WhatsApp",
        // },
        // {
        //   key: "contests_participated",
        //   sortable: true,
        //   label: "Participou",
        // },
        // {
        //   key: "contest_won",
        //   sortable: true,
        //   label: "Ganhou",
        // },
        // {
        //   key: "value_bet",
        //   sortable: true,
        //   label: "Valor investido",
        // },
        {
          key: "actions",
          sortable: false,
          label: "Ações",
        },
      ],
      items: [],
    };
  },
};
</script>

<style>
</style>