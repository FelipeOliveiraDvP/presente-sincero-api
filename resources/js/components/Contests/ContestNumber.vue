<template>
  <span
    class="contest-number"
    :class="numberClass(number.status)"
    @click="$emit('select', number)"
  >
    {{ number.number }}
    <button
      v-if="allowRemove"
      class="remove-btn bg-danger text-white"
      @click="$emit('remove', number)"
    >
      <font-awesome-icon icon="fa-solid fa-trash" />
    </button>
  </span>
</template>

<script>
export default {
  name: "ContestNumber",
  props: {
    number: Object,
    allowRemove: Boolean,
  },
  methods: {
    numberClass(status) {
      const bg = {
        FREE: "bg-success",
        RESERVED: "bg-warning",
        PAID: "bg-danger",
      };

      return bg[status];
    },
  },
};
</script>

<style>
</style>