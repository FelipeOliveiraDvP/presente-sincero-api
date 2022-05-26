<template>
  <b-card no-body class="contest-card overflow-hidden mb-3">
    <b-row no-gutters>
      <b-col md="6">
        <b-card-img :src="thumbnail" :alt="title" />
      </b-col>
      <b-col md="6">
        <b-card-body :title="title">
          <b-card-text>
            {{ description }}
          </b-card-text>
          <router-link :to="`sorteios/${slug}`">
            <b-button variant="primary">PARTICIPAR</b-button>
          </router-link>
        </b-card-body>
      </b-col>
    </b-row>
    <!-- <b-card-text>
      {{ description }}
    </b-card-text>

    <router-link :to="`sorteios/${slug}`">
      <b-button variant="primary">PARTICIPAR</b-button>
    </router-link> -->
  </b-card>
</template>

<script>
export default {
  props: {
    title: String,
    description: String,
    slug: String,
    gallery: Array,
  },
  name: "ContestCard",
  computed: {
    thumbnail() {
      const thumb = this.gallery.find((img) => img.thumbnail === true);

      return thumb ? thumb.image_path : this.gallery[0].image_path;
    },
  },
};
</script>

<style>
</style>