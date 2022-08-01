<template>
  <div>
    <div v-if="loading">
      <a-skeleton-input
        size="large"
        active
        block
        style="height: 40px; width: 100%"
      />
      <a-row :gutter="[16, 16]">
        <a-col :xs="24" :md="12" :lg="8">
          <a-skeleton-image />
        </a-col>
        <a-col :xs="24" :md="12" :lg="16">
          <a-skeleton-input
            size="large"
            active
            block
            style="height: 30px; width: 100%"
          />
          <a-skeleton-input
            size="large"
            active
            block
            style="height: 30px; width: 100%"
          />
          <a-skeleton-input
            size="large"
            active
            block
            style="height: 30px; width: 100%"
          />
          <a-skeleton-input
            size="large"
            active
            block
            style="height: 30px; width: 100%"
          />
          <a-skeleton-input
            size="large"
            active
            block
            style="height: 30px; width: 100%"
          />
          <a-skeleton-input
            size="large"
            active
            block
            style="height: 80px; width: 100%"
          />
        </a-col>
      </a-row>
      <a-skeleton-input
        size="large"
        active
        block
        style="height: 80px; width: 100%"
      />
    </div>
    <div v-else>
      <slot></slot>
    </div>
  </div>
</template>

<script>
import { defineComponent, toRefs } from "vue";

export default defineComponent({
  name: "ContestDetailSkeleton",
  props: {
    loading: Boolean,
  },
  setup(props) {
    const { loading } = toRefs(props);

    return {
      loading,
    };
  },
});
</script>

<style>
</style>