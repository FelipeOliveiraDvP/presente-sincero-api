<template>
  <a-modal
    v-model:visible="show"
    :title="account ? 'Editar conta' : 'Nova conta'"
    ok-text="Salvar"
    cancel-text="Cancelar"
    destroyOnClose
    :confirmLoading="loading"
    :cancelButtonProps="{ loading: loading }"
    @ok="handleSubmit"
    @cancel="handleCancel"
  >
    <a-form
      ref="formRef"
      :model="formState"
      layout="vertical"
      name="bank_account_form"
    >
      <a-form-item
        name="type"
        label="Tipo da conta"
        :rules="[{ required: true, message: 'Selecione o tipo da conta' }]"
      >
        <a-select v-model:value="formState.type" placeholder="Tipo da conta">
          <a-select-option value="BANK">Conta bancária</a-select-option>
          <a-select-option value="PIX">PIX</a-select-option>
        </a-select>
      </a-form-item>

      <a-form-item
        name="name"
        label="Nome"
        :rules="[{ required: true, message: 'Informe um nome para a conta' }]"
      >
        <a-input v-model:value="formState.name" />
      </a-form-item>

      <a-form-item
        v-if="formState.type === 'BANK'"
        name="cc"
        label="Conta corrente"
        :rules="[{ required: true, message: 'Informe o número da conta' }]"
      >
        <a-input v-model:value="formState.cc" />
      </a-form-item>

      <a-form-item
        v-if="formState.type === 'BANK'"
        name="agency"
        label="Agência"
        :rules="[{ required: true, message: 'Informe o número da agência' }]"
      >
        <a-input v-model:value="formState.agency" />
      </a-form-item>

      <a-form-item
        v-if="formState.type === 'BANK'"
        name="dv"
        label="Dígito verificador"
        :rules="[{ required: true, message: 'Informe o dígito verificador' }]"
      >
        <a-input v-model:value="formState.dv" />
      </a-form-item>

      <a-form-item
        v-if="formState.type === 'PIX'"
        name="key"
        label="Chave PIX"
        :rules="[
          {
            required: true,
            message: 'Informe a chave PIX para receber pagamentos',
          },
        ]"
      >
        <a-input v-model:value="formState.key" />
      </a-form-item>

      <a-form-item name="main" label="Conta principal">
        <a-switch v-model:checked="formState.main" />
      </a-form-item>
    </a-form>
  </a-modal>
</template>

<script lang="ts">
import {
  defineComponent,
  onMounted,
  PropType,
  reactive,
  ref,
  toRefs,
  watch,
} from "@vue/runtime-core";
import {
  BankAccountItem,
  BankAccountRequest,
  BankAccountResponse,
} from "@/types/BankAccount.types";
import { FormInstance, notification } from "ant-design-vue";
import { createBankAccount, editBankAccount } from "@/services/bankAccounts";
import { ErrorResponse } from "@/types/api.types";

export default defineComponent({
  name: "BankAccountModal",
  props: {
    account: {
      type: Object as PropType<BankAccountItem>,
    },
    visible: {
      type: Boolean,
    },
  },
  emits: ["finish", "cancel"],
  setup(props, { emit }) {
    const { account, visible } = toRefs(props);

    const show = ref<boolean>(false);
    const loading = ref<boolean>(false);
    const formRef = ref<FormInstance>();

    const formState = reactive<BankAccountRequest>({
      type: "BANK",
      name: "",
      main: false,
      cc: "",
      agency: "",
      dv: "",
      key: "",
    });

    async function handleSubmit() {
      try {
        if (formRef === undefined) return;
        loading.value = true;

        let result: BankAccountResponse;

        const values: BankAccountRequest =
          // @ts-ignore
          (await formRef?.value.validateFields()) as BankAccountRequest;

        if (account.value?.id === undefined) {
          result = await createBankAccount(values);
        } else {
          result = await editBankAccount(account.value.id, values);
        }

        notification.success({
          message: result.message,
        });

        // @ts-ignore
        formRef.value.resetFields();

        emit("finish", result.bank_account);
      } catch (error: unknown) {
        // const { message } = error as ErrorResponse;
        // notification.error({
        //   message: message,
        // });
      } finally {
        loading.value = false;
      }
    }

    function handleCancel() {
      // @ts-ignore
      formRef.value.resetFields();
      emit("cancel");
    }

    watch(visible, (newVal) => {
      show.value = newVal;
    });

    watch(account, (newVal) => {
      formState.type = newVal?.type || "BANK";
      formState.name = newVal?.name || "";
      formState.main = newVal?.main || false;
      formState.cc = newVal?.cc || "";
      formState.agency = newVal?.agency || "";
      formState.dv = newVal?.dv || "";
      formState.key = newVal?.key || "";
    });

    return {
      account,
      show,
      loading,
      formState,
      formRef,
      handleSubmit,
      handleCancel,
    };
  },
});
</script>

<style>
</style>