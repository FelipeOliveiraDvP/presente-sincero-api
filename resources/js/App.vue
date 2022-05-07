<template>
  <div>
    <site-header />
    <router-view />
    <public-footer />
  </div>
</template>

<script>
import HeaderVue from "./components/_commons/Header.vue";
import PublicFooterVue from "./components/_commons/PublicFooter.vue";

export default {
  name: "App",
  components: {
    "site-header": HeaderVue,
    "public-footer": PublicFooterVue,
  },
};
</script>

<style>
</style>