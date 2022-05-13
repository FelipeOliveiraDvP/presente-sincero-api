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
          key: "name",
          sortable: true,
          label: "Nome",
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
      items: [
        {
          name: "Paulo da Silva",
          whatsapp: "(11) 99999 5555",
          contests_participated: 5,
          contest_won: 0,
          value_bet: "R$ 15,00",
          actions: "TEste",
        },
        {
          name: "Paulo da Silva",
          whatsapp: "(11) 99999 5555",
          contests_participated: 5,
          contest_won: 0,
          value_bet: "R$ 15,00",
          actions: "TEste",
        },
        {
          name: "Paulo da Silva",
          whatsapp: "(11) 99999 5555",
          contests_participated: 5,
          contest_won: 0,
          value_bet: "R$ 15,00",
          actions: "TEste",
        },
        {
          name: "Paulo da Silva",
          whatsapp: "(11) 99999 5555",
          contests_participated: 5,
          contest_won: 0,
          value_bet: "R$ 15,00",
          actions: "TEste",
        },
        {
          name: "Paulo da Silva",
          whatsapp: "(11) 99999 5555",
          contests_participated: 5,
          contest_won: 0,
          value_bet: "R$ 15,00",
          actions: "TEste",
        },
        {
          name: "Paulo da Silva",
          whatsapp: "(11) 99999 5555",
          contests_participated: 5,
          contest_won: 0,
          value_bet: "R$ 15,00",
          actions: "TEste",
        },
        {
          name: "Paulo da Silva",
          whatsapp: "(11) 99999 5555",
          contests_participated: 5,
          contest_won: 0,
          value_bet: "R$ 15,00",
          actions: "TEste",
        },
        {
          name: "Paulo da Silva",
          whatsapp: "(11) 99999 5555",
          contests_participated: 5,
          contest_won: 0,
          value_bet: "R$ 15,00",
          actions: "TEste",
        },
        {
          name: "Paulo da Silva",
          whatsapp: "(11) 99999 5555",
          contests_participated: 5,
          contest_won: 0,
          value_bet: "R$ 15,00",
          actions: "TEste",
        },
        {
          name: "Paulo da Silva",
          whatsapp: "(11) 99999 5555",
          contests_participated: 5,
          contest_won: 0,
          value_bet: "R$ 15,00",
          actions: "TEste",
        },
      ],
    };
  },
};
</script>

<style>
</style>