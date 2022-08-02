<template>
  <a-modal
    v-model:visible="show"
    title="Remover conta"
    ok-text="Excluir conta"
    cancel-text="Cancelar"
    destroyOnClose
    :confirmLoading="loading"
    :okButtonProps="{ danger: true }"
    :cancelButtonProps="{ loading: loading }"
    @ok="handleRemove(account)"
    @cancel="handleCancel"
  >
    <a-typography-paragraph>
      Deseja remover a conta "{{ account.name }}"?
    </a-typography-paragraph>
    <a-typography-paragraph>
      <strong>Após remover não será possível recuperar.</strong>
    </a-typography-paragraph>
  </a-modal>
</template>

<script lang="ts">
import { BankAccountItem } from "@/types/BankAccount.types";
import {
  defineComponent,
  PropType,
  ref,
  toRefs,
  watch,
} from "@vue/runtime-core";

export default defineComponent({
  name: "RemoveAccountModal",
  props: {
    account: {
      type: Object as PropType<BankAccountItem>,
    },
    visible: {
      type: Boolean,
    },
    loading: {
      type: Boolean,
    },
  },
  emits: ["remove", "cancel"],
  setup(props, { emit }) {
    const show = ref<boolean>(false);
    const { account, visible, loading } = toRefs(props);

    function handleRemove(account?: BankAccountItem) {
      emit("remove", account);
    }

    function handleCancel() {
      emit("cancel");
    }

    watch(visible, (newVal) => {
      show.value = newVal;
    });

    return {
      account,
      show,
      loading,
      handleRemove,
      handleCancel,
    };
  },
});
</script>

<style>
</style>