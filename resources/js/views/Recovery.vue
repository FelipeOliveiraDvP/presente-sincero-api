<template>
  <container :center="true">
    <recovery-form :loading="loading" @onFinish="handleFinish" />
  </container>
</template>

<script lang="ts">
import { defineComponent, ref } from "@vue/runtime-core";

import Container from "@/components/_commons/Container.vue";
import RecoveryForm from "@/components/Auth/RecoveryForm.vue";
import { AuthRequest } from "@/types/Auth.types";
import { forgot } from "@/services/auth";
import { notification } from "ant-design-vue";
import { ErrorResponse } from "@/types/api.types";

export default defineComponent({
  components: { Container, RecoveryForm },
  name: "Recovery",
  setup() {
    const loading = ref<boolean>(false);

    function handleFinish(values: AuthRequest) {
      forgot(values)
        .then((result) => {
          loading.value = true;

          console.log(result);
        })
        .catch((error) => {
          console.log(error);
        })
        .finally(() => (loading.value = false));

      // try {
      //   loading.value = true;

      //   await

      //   // notification.success({
      //   //   message: result.message,
      //   // });
      // } catch (error) {
      //   // const { message } = error as ErrorResponse;
      //   // notification.error({
      //   //   message,
      //   // });
      // } finally {
      //   loading.value = false;
      // }
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