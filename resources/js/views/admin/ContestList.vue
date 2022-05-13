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
      :per-page="params.perPage"
      :current-page="params.currentPage"
    ></b-table>

    <b-pagination
      v-model="params.currentPage"
      :total-rows="params.total"
      :per-page="params.perPage"
      aria-controls="contest-list"
    ></b-pagination>
  </div>
</template>

<script>
export default {
  name: "AdminContestList",
  data() {
    return {
      params: {
        title: "",
        date: null,
        total: 10,
        currentPage: 1,
        perPage: 5,
      },
      fields: [
        {
          key: "title",
          sortable: true,
          label: "Título",
        },
        {
          key: "start_date",
          sortable: true,
          label: "Data de início",
        },
        {
          key: "paid_numbers",
          sortable: true,
          label: "Números pagos",
        },
        {
          key: "price",
          sortable: true,
          label: "Preço R$",
        },
        {
          key: "actions",
          sortable: false,
          label: "Ações",
        },
      ],
      items: [
        {
          title: "Sorteio iPhone 13",
          start_date: "25/10/2022",
          paid_numbers: "85%",
          price: "5,00",
          actions: "TEste",
        },
        {
          title: "Sorteio iPhone 13",
          start_date: "25/10/2022",
          paid_numbers: "85%",
          price: "5,00",
          actions: "TEste",
        },
        {
          title: "Sorteio iPhone 13",
          start_date: "25/10/2022",
          paid_numbers: "85%",
          price: "5,00",
          actions: "TEste",
        },
        {
          title: "Sorteio iPhone 13",
          start_date: "25/10/2022",
          paid_numbers: "85%",
          price: "5,00",
          actions: "TEste",
        },
        {
          title: "Sorteio iPhone 13",
          start_date: "25/10/2022",
          paid_numbers: "85%",
          price: "5,00",
          actions: "TEste",
        },
        {
          title: "Sorteio iPhone 11",
          start_date: "25/10/2022",
          paid_numbers: "85%",
          price: "5,00",
          actions: "TEste",
        },
        {
          title: "Sorteio iPhone 11",
          start_date: "25/10/2022",
          paid_numbers: "85%",
          price: "5,00",
          actions: "TEste",
        },
        {
          title: "Sorteio iPhone 11",
          start_date: "25/10/2022",
          paid_numbers: "85%",
          price: "5,00",
          actions: "TEste",
        },
        {
          title: "Sorteio iPhone 11",
          start_date: "25/10/2022",
          paid_numbers: "85%",
          price: "5,00",
          actions: "TEste",
        },
        {
          title: "Sorteio iPhone 11",
          start_date: "25/10/2022",
          paid_numbers: "85%",
          price: "5,00",
          actions: "TEste",
        },
      ],
    };
  },
};
</script>

<style>
</style>