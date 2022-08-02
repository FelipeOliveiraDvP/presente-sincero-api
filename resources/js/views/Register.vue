<template>
  <container :center="true">
    <register-form :loading="loading" @onFinish="handleFinish" />
  </container>
</template>

<script lang="ts">
import RegisterForm from "@/components/Auth/RegisterForm.vue";
import Container from "@/components/_commons/Container.vue";
import { register } from "@/services/auth";
import { useAuthStore } from "@/store/auth";

import { AuthRegisterRequest } from "@/types/Auth.types";
import { getErrorMessage } from "@/utils/handleError";
import { defineComponent, ref } from "@vue/runtime-core";
import { notification } from "ant-design-vue";

export default defineComponent({
  components: { Container, RegisterForm },
  name: "Register",
  setup() {
    const { login: signIn } = useAuthStore();
    const loading = ref<boolean>(false);

    async function handleFinish(values: AuthRegisterRequest) {
      try {
        loading.value = true;
        const result = await register(values);

        signIn(result);
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