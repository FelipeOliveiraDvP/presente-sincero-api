<template>
  <container :center="true">
    <recovery-form :loading="loading" @onFinish="handleFinish" />
  </container>
</template>

<script lang="ts">
import { defineComponent, ref } from "@vue/runtime-core";
import { notification } from "ant-design-vue";

import Container from "@/components/_commons/Container.vue";
import RecoveryForm from "@/components/Auth/RecoveryForm.vue";

import { forgot } from "@/services/auth";
import { AuthRequest } from "@/types/Auth.types";
import { ApiResponse, ErrorResponse } from "@/types/api.types";
import { getErrorMessage } from "@/utils/handleError";

export default defineComponent({
  components: { Container, RecoveryForm },
  name: "Recovery",
  setup() {
    const loading = ref<boolean>(false);

    async function handleFinish(values: AuthRequest) {
      try {
        loading.value = true;

        const result: ApiResponse = await forgot(values);

        notification.success({
          message: result.message,
        });
      } catch (error: unknown) {
        notification.error({
          message: getErrorMessage(error),
        });
      } finally {
        loading.value = false;
      }
    }

    return {
      loading,
      handleFinish,
    };
  },
});
</script>

<style>
</style>