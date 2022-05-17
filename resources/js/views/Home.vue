<template>
  <b-container class="page">
    <h2>Últimos Sorteios</h2>
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

    <h2>Últimos Ganhadores</h2>
    <b-row class="mb-4">
      <b-col v-for="i in 3" :key="i" md="6" lg="4"><winner-card /></b-col>
    </b-row>
  </b-container>
</template>

<script>
import ContestCardVue from "../components/Contests/ContestCard.vue";
import WinnerCardVue from "../components/Winners/WinnerCard.vue";

import { listContests } from "../services/contests";

export default {
  name: "Home",
  components: {
    "contest-card": ContestCardVue,
    "winner-card": WinnerCardVue,
  },
  data() {
    return {
      loading: false,
      params: {
        limit: 4,
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