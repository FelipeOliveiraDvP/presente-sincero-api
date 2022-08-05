<template>
  <a-modal
    v-model:visible="show"
    title="Finalizar compra"
    :ok-text="formState.new_account ? 'Criar conta' : 'Acessar minha conta'"
    cancel-text="Cancelar"
    destroyOnClose
    :confirmLoading="loading"
    :cancelButtonProps="{ loading: loading }"
    @ok="handleValidate"
    @cancel="handleCancel"
  >
    <a-typography-paragraph v-if="formState.new_account">
      Informe seu nome e Whatsapp para a finalizar a compra.
    </a-typography-paragraph>
    <a-typography-paragraph v-else>
      Informe seu Whatsapp para a finalizar a compra.
    </a-typography-paragraph>

    <a-form
      ref="formRef"
      :model="formState"
      layout="vertical"
      name="simple_login_form"
    >
      <a-form-item
        label="WhatsApp"
        name="whatsapp"
        :rules="[
          { required: true, message: 'Campo obrigatório!' },
          { pattern: /^[0-9]{11}$/, message: 'Informe um número válido' },
        ]"
      >
        <a-input v-model:value="formState.whatsapp" />
      </a-form-item>
      <a-form-item
        v-if="formState.new_account"
        label="Nome"
        name="name"
        :rules="[{ required: true, message: 'Campo obrigatório!' }]"
      >
        <a-input v-model:value="formState.name" />
      </a-form-item>
    </a-form>

    <a-typography-paragraph>
      <strong>Importante:</strong> Tenha certeza de nos informar o WhatsApp
      correto, pois é através dele que enviaremos seu comprovante.
    </a-typography-paragraph>
  </a-modal>
</template>

<script lang="ts">
import { simpleLogin } from "@/services/auth";
import { useAuthStore } from "@/store/auth";
import { AuthSimpleLoginRequest } from "@/types/Auth.types";
import { getErrorMessage } from "@/utils/handleError";

import {
  defineComponent,
  reactive,
  ref,
  toRefs,
  watch,
} from "@vue/runtime-core";
import { FormInstance, notification } from "ant-design-vue";

export default defineComponent({
  name: "SimpleRegisterModal",
  props: {
    visible: {
      type: Boolean,
    },
  },
  emits: ["success", "cancel"],
  setup(props, { emit }) {
    const { visible } = toRefs(props);
    const { simpleLogin: signIn } = useAuthStore();
    const formRef = ref<FormInstance>();
    const show = ref<boolean>(false);
    const loading = ref<boolean>(false);
    const formState = reactive<AuthSimpleLoginRequest>({
      whatsapp: "",
      name: "",
      new_account: false,
    });

    async function handleValidate() {
      try {
        const values =
          (await formRef.value.validateFields()) as AuthSimpleLoginRequest;
        await handleSubmit(values);
      } catch (error) {}
    }

    async function handleSubmit(values: AuthSimpleLoginRequest) {
      try {
        if (formRef === undefined) return;
        loading.value = true;

        const result = await simpleLogin({ ...values, ...formState });

        if ("new_account" in result && result.new_account === true) {
          formState.new_account = true;
        } else {
          signIn(result);
          emit("success");
        }
      } catch (error) {
        notification.error({
          message: getErrorMessage(error),
        });
      } finally {
        loading.value = false;
      }
    }

    function handleCancel() {
      formRef.value.resetFields();
      emit("cancel");
    }

    watch(visible, (newVal) => {
      show.value = newVal;
    });

    return {
      show,
      loading,
      formRef,
      formState,
      handleValidate,
      handleCancel,
    };
  },
});
</script>

<style>
</style>