<template>
  <div class="pagination-wrapper">
    <a-pagination
      v-model:current="pager.currentPage"
      :total="pager.total"
      :pageSize="pager.pageSize"
      @change="handlePaginate"
    />
  </div>
</template>

<script lang="ts">
import {
  defineComponent,
  PropType,
  reactive,
  toRefs,
  watchEffect,
} from "@vue/runtime-core";

interface PaginationProps {
  current_page: number;
  from: number;
  to: number;
  last_page: number;
  per_page: number;
  total: number;
}

export default defineComponent({
  name: "pagination",
  emits: ["paginate"],
  props: {
    pager: {
      type: Object as PropType<PaginationProps>,
    },
  },
  setup(props, { emit }) {
    const {
      pager: { value },
    } = toRefs(props);

    const pager = reactive({
      total: value ? value?.total : 1,
      pageSize: value ? Number(value?.per_page) : 1,
      currentPage: value ? value?.current_page : 1,
    });

    function handlePaginate(page: number) {
      emit("paginate", page);
    }

    watchEffect(() => {
      pager.total = value ? value?.total : 1;
      pager.pageSize = value ? Number(value?.per_page) : 1;
      pager.currentPage = value ? value?.current_page : 1;
    });

    return {
      pager,
      handlePaginate,
    };
  },
});
</script>

<style>
.pagination-wrapper {
  display: flex;
  justify-content: flex-end;
  margin: 1rem 0;
}
</style>