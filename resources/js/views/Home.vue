<template>
  <b-container class="page">
    <h2>Últimos Sorteios</h2>
    <b-row>
      <b-col v-for="i in 4" :key="i" md="4" lg="3">
        <contest-card />
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

export default {
  name: "Home",
  components: {
    "contest-card": ContestCardVue,
    "winner-card": WinnerCardVue,
  },
};
</script>

<style>
</style>