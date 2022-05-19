<template>
  <b-card
    :title="title"
    :img-src="thumbnail"
    :img-alt="title"
    img-top
    tag="article"
    class="mb-3"
  >
    <b-card-text>
      {{ description }}
    </b-card-text>

    <router-link :to="`sorteios/${slug}`">
      <b-button variant="success">PARTICIPAR</b-button>
    </router-link>
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