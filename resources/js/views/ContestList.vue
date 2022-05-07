<template>
  <b-container class="page">
    <div class="my-4 d-flex flex-column flex-md-row justify-content-between">
      <b-button-group>
        <b-button variant="success">Em andamento</b-button>
        <b-button variant="warning">Próximos</b-button>
        <b-button variant="danger">Encerrados</b-button>
      </b-button-group>

      <b-form-select v-model="order" :options="options"></b-form-select>
    </div>

    <b-row>
      <b-col v-for="i in 16" :key="i" md="4" lg="3">
        <contest-card />
      </b-col>
    </b-row>

    <div class="text-center my-4">
      <b-button variant="primary">CARREGAR MAIS</b-button>
    </div>
  </b-container>
</template>

<script>
import ContestCardVue from "../components/Contests/ContestCard.vue";

export default {
  name: "ContestList",
  data() {
    return {
      order: null,
      options: [
        { value: null, text: "Ordenar sorteios por" },
        { value: "a", text: "Maior prêmiação" },
        { value: "b", text: "Menor valor do número" },
      ],
    };
  },
  components: {
    "contest-card": ContestCardVue,
  },
};
</script>

<style>
</style>