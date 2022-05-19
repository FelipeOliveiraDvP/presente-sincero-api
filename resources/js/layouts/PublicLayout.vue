<template>
  <div>
    <site-header />
    <main>
      <router-view />
    </main>
    <site-footer />
  </div>
</template>

<script>
import HeaderVue from "../components/_commons/Header.vue";
import FooterVue from "../components/_commons/Footer.vue";

export default {
  name: "PublicLayout",
  components: {
    "site-header": HeaderVue,
    "site-footer": FooterVue,
  },
};
</script>

<style>
</style>