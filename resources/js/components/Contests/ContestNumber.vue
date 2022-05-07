<template>
  <b-col cols="4" md="2" lg="1" class="border p-2 text-center">
    {{ value }}
  </b-col>
</template>

<script>
export default {
  name: "ContestNumber",
  props: {
    value: Number,
  },
};
</script>

<style>
</style>