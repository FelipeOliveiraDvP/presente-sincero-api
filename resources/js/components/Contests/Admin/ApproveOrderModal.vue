<template>
  <a-modal
    v-model:visible="show"
    title="Aprovar pedido"
    ok-text="Aprovar pagamento"
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
      Após clicar em "Aprovar pagamento", o pedido será aprovado e o cliente
      será notificado por WhatsApp. <br />
      Portanto, é necessário verificar o comprovante enviado pelo cliente e se
      certificar de que o pagamento foi debitado em sua conta. Está ação
      <strong>NÃO</strong> pode ser revertida.
    </a-typography-paragraph>
    <a-typography-paragraph>
      <strong>Deseja aprovar o pagamento do pedido?</strong>
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
  name: "ApproveOrderModal",
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