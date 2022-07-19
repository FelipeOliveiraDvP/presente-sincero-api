<template>
  <div>ContestCard</div>
  <!-- <b-card no-body class="contest-card overflow-hidden mb-3">
    <b-row no-gutters>
      <b-col md="6">
        <b-card-img :src="thumbnail" :alt="contest.title" />
      </b-col>
      <b-col md="6">
        <b-card-body :title="contest.title">
          <b-card-text>
            {{ contest.short_description }}
          </b-card-text>
          <router-link :to="`sorteios/${contest.slug}`">
            <b-button variant="primary">PARTICIPAR</b-button>
          </router-link>
        </b-card-body>
      </b-col>
    </b-row>
  </b-card> -->
</template>

<script>
export default {
  props: {
    contest: Object,
  },
  name: "ContestCard",
  computed: {
    thumbnail() {
      const { gallery } = this.contest;

      return gallery[0].path;
    },
  },
};
</script>

<style>
</style>