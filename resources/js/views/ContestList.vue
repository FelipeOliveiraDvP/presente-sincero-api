<template>
  <b-container class="page">
    <div class="my-4 d-flex flex-column flex-md-row justify-content-between">
      <b-button-group class="mb-3">
        <b-button variant="success">Em andamento</b-button>
        <b-button variant="warning">Próximos</b-button>
        <b-button variant="danger">Encerrados</b-button>
      </b-button-group>

      <b-form-select
        class="custom-form-select"
        v-model="order"
        :options="options"
      ></b-form-select>
    </div>

    <my-loader v-if="loading" />
    <b-row v-else>
      <b-col v-for="contest in contests" :key="contest.id" md="6">
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
import LoaderVue from "../components/_commons/Loader.vue";
import { listContests } from "../services/contests";

export default {
  name: "ContestList",
  components: {
    "contest-card": ContestCardVue,
    "my-loader": LoaderVue,
  },
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
};
</script>

<style>
</style>