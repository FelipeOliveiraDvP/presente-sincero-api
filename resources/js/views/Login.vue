<template>
  <container :center="true">
    <login-form :loading="loading" @onFinish="handleFinish" />
  </container>
</template>

<script lang="ts">
import { defineComponent, ref } from "@vue/runtime-core";

import Container from "@/components/_commons/Container.vue";
import LoginForm from "@/components/Auth/LoginForm.vue";
import { AuthLoginRequest } from "@/types/Auth.types";
import { useAuthStore } from "@/store/auth";
import { login } from "@/services/auth";
import { notification } from "ant-design-vue";
import { ErrorResponse } from "@/types/api.types";

export default defineComponent({
  name: "Login",
  components: { Container, LoginForm },
  setup() {
    const { login: signIn } = useAuthStore();
    const loading = ref<boolean>(false);

    async function handleFinish(values: AuthLoginRequest) {
      try {
        loading.value = true;
        const result = await login(values);

        signIn(result);
      } catch (error: unknown) {
        const { message } = error as ErrorResponse;

        notification.error({
          message,
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