<template>
  <a-modal
    v-model:visible="show"
    title="Cancelar pedido"
    ok-text="Cancelar pedido"
    cancel-text="Cancelar"
    destroyOnClose
    :confirmLoading="loading"
    :cancelButtonProps="{ loading: loading }"
    :closable="false"
    :maskClosable="false"
    @ok="handleApprove(order)"
    @cancel="handleCancel"
  >
    <a-typography-paragraph>
      Após clicar em "Cancelar pedido", o pedido será cancelado e os números
      reservados voltarão a ficar disponíveis para venda. <br />
      Portanto, é necessário entrar em contato com o cliente e confirmar se o
      mesmo não tem interesse em efetivar o pagamento. Está ação
      <strong>NÃO</strong> pode ser revertida.
    </a-typography-paragraph>
    <a-typography-paragraph>
      <strong
        >Deseja realmente CANCELAR o pedido e LIBERAR OS NÚMEROS reservados pelo
        cliente?</strong
      >
    </a-typography-paragraph>
  </a-modal>
</template>

<script lang="ts">
import { ContestOrderItem } from "@/types/Contest.types";
import {
  defineComponent,
  PropType,
  ref,
  toRefs,
  watch,
} from "@vue/runtime-core";

export default defineComponent({
  name: "CancelSellerModal",
  props: {
    order: {
      type: Object as PropType<ContestOrderItem>,
    },
    visible: {
      type: Boolean,
    },
    loading: {
      type: Boolean,
    },
  },
  emits: ["approve", "cancel"],
  setup(props, { emit }) {
    const show = ref<boolean>(false);
    const { order, visible, loading } = toRefs(props);

    function handleApprove(order?: ContestOrderItem) {
      emit("approve", order);
    }

    function handleCancel() {
      emit("cancel");
    }

    watch(visible, (newVal) => {
      show.value = newVal;
    });

    return {
      order,
      show,
      loading,
      handleApprove,
      handleCancel,
    };
  },
});
</script>

<style>
</style>