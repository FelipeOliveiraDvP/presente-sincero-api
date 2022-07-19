<template>
  <h1>Sorteios</h1>
  <!-- <b-container class="page">
    

    <h2 class="text-center">Sorteios em andamento</h2>

    <my-loader v-if="loading" />
    <b-row v-else>
      <b-col v-for="contest in contests" :key="contest.id" md="6">
        <contest-card :contest="contest" />
      </b-col>
    </b-row>
  </b-container> -->
</template>

<script>
import LoaderVue from "@/components/_commons/Loader.vue";
import ContestCardVue from "@/components/Contests/ContestCard.vue";

import { listContests } from "@/services/contests";

export default {
  name: "ContestList",
  components: {
    "contest-card": ContestCardVue,
    "my-loader": LoaderVue,
  },
  data() {
    return {
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