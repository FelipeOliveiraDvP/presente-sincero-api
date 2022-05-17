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
      <b-col v-for="contest in contests" :key="contest.id" md="4" lg="3">
        <contest-card
          :title="contest.title"
          :description="contest.short_description"
          :slug="contest.slug"
          :gallery="contest.gallery"
        />
      </b-col>
    </b-row>

    <div class="text-center my-4">
      <b-button variant="primary">CARREGAR MAIS</b-button>
    </div>
  </b-container>
</template>

<script>
import ContestCardVue from "../components/Contests/ContestCard.vue";
import { listContests } from "../services/contests";

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
      loading: false,
      params: {
        limit: 12,
      },
      contests: [],
    };
  },
  mounted() {
    this.getContests();
  },
  methods: {
    async getContests() {
      this.loading = true;

      const result = await listContests(this.params);

      this.contests = result.data;
      this.loading = false;
    },
  },
  components: {
    "contest-card": ContestCardVue,
  },
};
</script>

<style>
</style>