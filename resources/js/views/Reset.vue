<template>
  <container :center="true">
    <a-alert
      v-if="validateCode.isValid === false"
      :message="loading ? 'Verificando o código...' : validateCode.message"
      :type="loading ? 'warning' : 'error'"
      style="width: 100%; max-width: 600px; padding: 20px; text-align: center"
      closeText="Voltar para o login"
      @close="handleClose"
    />

    <reset-form
      v-else
      :loading="loading"
      :code="code"
      @onFinish="handleFinish"
    />
  </container>
</template>

<script lang="ts">
import { defineComponent, onMounted, reactive, ref } from "@vue/runtime-core";

import ResetForm from "@/components/Auth/ResetForm.vue";
import Container from "@/components/_commons/Container.vue";
import { useRoute, useRouter } from "vue-router";
import { AuthResetRequest } from "@/types/Auth.types";
import { reset, verify } from "@/services/auth";
import { useAuthStore } from "@/store/auth";
import { ErrorResponse } from "@/types/api.types";
import { notification } from "ant-design-vue";

export default defineComponent({
  components: { Container, ResetForm },
  name: "Reset",
  setup() {
    const route = useRoute();
    const router = useRouter();
    const { code } = route.params;
    const { login: signIn } = useAuthStore();
    const loading = ref<boolean>();
    const validateCode = reactive({
      isValid: false,
      message: "",
    });

    async function handleFinish(values: AuthResetRequest) {
      try {
        loading.value = true;
        const result = await reset(values);

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

    function handleClose() {
      router.push({ name: "login" });
    }

    onMounted(async () => {
      try {
        loading.value = true;

        await verify(`${code}`);

        validateCode.isValid = true;
      } catch (error: unknown) {
        const { message } = error as ErrorResponse;

        validateCode.isValid = false;
        validateCode.message = message as string;
      } finally {
        loading.value = false;
      }
    });

    return {
      code,
      loading,
      validateCode,
      handleFinish,
      handleClose,
    };
  },
});
</script>

<style>
</style>