<template>
  <b-card
    title="Super sorteio iPhone 13"
    img-src="http://lorempixel.com.br/600/600"
    img-alt="Image"
    img-top
    tag="article"
    class="mb-3"
  >
    <b-card-text>
      Some quick example text to build on the card title and make up the bulk of
      the card's content.
    </b-card-text>

    <router-link to="sorteios/sorteio-iphone">
      <b-button variant="success">PARTICIPAR</b-button>
    </router-link>
  </b-card>
</template>

<script>
export default {
  name: "ContestCard",
};
</script>

<style>
</style>