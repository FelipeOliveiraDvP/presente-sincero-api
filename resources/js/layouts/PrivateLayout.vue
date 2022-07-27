<template>
  <div>
    <h2>PrivateLayout</h2>
    <router-view />
  </div>
</template>

<script lang="ts">
import { defineComponent } from "@vue/runtime-core";

export default defineComponent({
  name: "PrivateLayout",
});
</script>

<style lang="scss">
</style>