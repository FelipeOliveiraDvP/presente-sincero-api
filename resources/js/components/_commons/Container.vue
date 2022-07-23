<template>
  <div class="container">
    <slot></slot>
  </div>
</template>

<script>
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'Container'
});
</script>

<style>
.container {
  padding: 16px 24px;
}
</style>