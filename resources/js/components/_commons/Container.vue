<template>
  <div :class="`container ${center && 'align-center'}`">
    <slot></slot>
  </div>
</template>

<script>
import { defineComponent } from "vue";

export default defineComponent({
  name: "Container",
  props: {
    center: {
      type: Boolean,
    },
  },
});
</script>

<style>
.container {
  padding: 16px 24px;
  width: 100%;
}
.align-center {
  min-height: 650px;
  display: flex;
  justify-content: center;
  align-items: center;
}
</style>