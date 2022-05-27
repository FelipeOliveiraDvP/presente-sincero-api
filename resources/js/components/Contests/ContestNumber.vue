<template>
  <span
    class="contest-number"
    :title="number.customer ? number.customer.name : ''"
    :class="isSelected ? 'bg-secondary' : numberClass(number.status)"
    @click="$emit('select', number)"
    v-b-tooltip.hover.bottom
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
    isSelected: Boolean,
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