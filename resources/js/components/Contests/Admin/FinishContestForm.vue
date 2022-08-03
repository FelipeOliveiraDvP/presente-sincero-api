<template>
  <a-typography-title :level="3"> Finalizar sorteio </a-typography-title>
  <a-typography-text type="secondary">
    Encerra o sorteio atual e define o vencedor.
  </a-typography-text>

  <a-form
    ref="formRef"
    :model="formState"
    layout="vertical"
    name="finish_contest_form"
    @finish="handleSubmit"
  >
    <a-form-item
      name="number"
      label="Número vencedor"
      :rules="[
        { required: true, message: 'Informe o número vencedor do sorteio' },
      ]"
    >
      <a-input v-model:value="formState.number" />
    </a-form-item>

    <a-form-item>
      <a-button type="primary" html-type="submit" :loading="loading"
        >Finalizar sorteio</a-button
      >
    </a-form-item>
  </a-form>
</template>

<script lang="ts">
import { finishContest } from "@/services/contests";
import { getErrorMessage } from "@/utils/handleError";
import { defineComponent, reactive, ref } from "@vue/runtime-core";
import { notification } from "ant-design-vue";
import { useRoute } from "vue-router";

export default defineComponent({
  name: "FinishContestForm",
  setup() {
    const route = useRoute();
    const { id } = route.params;
    const loading = ref<boolean>(false);
    const formState = reactive({
      number: "",
    });

    async function handleSubmit(values: { number: string }) {
      try {
        loading.value = true;

        const result = await finishContest(Number(id), values.number);

        notification.success({
          message: getErrorMessage(result.message),
        });
      } catch (error) {
        notification.error({
          message: getErrorMessage(error),
        });
      } finally {
        loading.value = false;
      }
    }

    return {
      formState,
      loading,
      handleSubmit,
    };
  },
});
</script>

<style>
</style>