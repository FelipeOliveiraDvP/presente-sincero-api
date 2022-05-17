<template>
  <div class="site">
    <private-layout v-if="isLogged">
      <router-view />
    </private-layout>

    <public-layout v-else>
      <router-view />
    </public-layout>
  </div>
</template>

<script>
import PublicLayoutVue from "./layouts/PublicLayout.vue";
import PrivateLayoutVue from "./layouts/PrivateLayout.vue";

export default {
  name: "App",
  data() {
    return {
      isLogged: false,
    };
  },
  components: {
    "public-layout": PublicLayoutVue,
    "private-layout": PrivateLayoutVue,
  },
};
</script>

<style>
</style>