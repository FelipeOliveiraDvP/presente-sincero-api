<template>
  <div class="pagination-wrapper">
    <a-pagination v-model:current="pager.current_page" :total="pager.total" show-less-items />
  </div>
</template>

<script>
import { defineComponent } from 'vue';

export default defineComponent({
  name: "pagination",
  props: {
    pager: {
      current_page: Number,
      from: Number,
      to: Number,
      last_page: Number,
      per_page: Number,
      total: Number
    }
  }
});
</script>

<style>
.pagination-wrapper {
  display: flex;
  justify-content: flex-end;
  margin: 1rem 0;
}
</style>