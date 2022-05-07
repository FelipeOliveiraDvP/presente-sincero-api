<template>
  <div>
    <site-header />
    <router-view />
  </div>
</template>

<script>
import HeaderVue from "./components/_commons/Header.vue";

export default {
  name: "App",
  components: {
    "site-header": HeaderVue,
  },
};
</script>

<style>
</style>