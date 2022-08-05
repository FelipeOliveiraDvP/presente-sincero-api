<template>
  <div>
    <div v-if="loading">
      <a-row :gutter="[16, 16]">
        <a-col :xs="24" :lg="12" :xl="8">
          <a-image src="/img/placeholder.jpg" :preview="false" />
        </a-col>
        <a-col :xs="24" :lg="12" :xl="16" class="column-placeholder">
          <a-space direction="vertical" style="width: 100%">
            <a-skeleton-input style="width: 100%" active size="large" />
            <a-skeleton style="width: 100%" active :paragraph="{ rows: 4 }" />
          </a-space>

          <a-row :gutter="[16, 16]">
            <a-col :xs="24" :lg="12" :xl="8">
              <a-skeleton-button active size="large" block />
            </a-col>
            <a-col :xs="24" :lg="12" :xl="8">
              <a-skeleton-button active size="large" block />
            </a-col>
            <a-col :xs="24" :lg="24" :xl="8">
              <a-skeleton-button active size="large" block />
            </a-col>
          </a-row>
        </a-col>
      </a-row>
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
.column-placeholder {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}
</style>