<template>
  <div>
    <section class="home-banner">
      <div class="banner-overlay">
        <b-container>
          <h1 class="fs-1">Bem vindo a Rifandos</h1>
          <p class="fs-4">A melhor plataforma de rifas online</p>
          <router-link to="/cadastre-se">
            <b-button variant="primary" size="lg">
              Comece a vender
            </b-button>
          </router-link>
        </b-container>
      </div>
      <img src="/img/banner.jpg" class="img-fluid w-100" />
    </section>

    <b-container class="page">
      <h2>Últimos Sorteios</h2>
      <my-loader v-if="loading" />
      <b-row v-else>
        <b-col v-for="contest in contests" :key="contest.id" md="6">
          <contest-card :contest="contest" />
        </b-col>
      </b-row>
    </b-container>
  </div>
</template>

<script>
import ContestCardVue from "@/components/Contests/ContestCard.vue";
import LoaderVue from "@/components/_commons/Loader.vue";

import { listContests } from "@/services/contests";

export default {
  name: "Home",
  components: {
    "contest-card": ContestCardVue,
    "my-loader": LoaderVue,
  },
  data() {
    return {
      loading: false,
      params: {
        limit: 8,
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