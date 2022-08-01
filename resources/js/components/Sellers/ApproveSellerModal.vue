<template>
  <a-modal
    v-model:visible="show"
    title="Aprovar vendedor"
    ok-text="Aprovar"
    cancel-text="Cancelar"
    destroyOnClose
    :confirmLoading="loading"
    :cancelButtonProps="{ loading: loading }"
    :closable="false"
    :maskClosable="false"
    @ok="handleApprove(seller)"
    @cancel="handleCancel"
  >
    <a-typography-paragraph>
      Após clicar em aprovar, o vendedor vai ser liberado para criar sorteios no
      site.
    </a-typography-paragraph>
    <a-typography-paragraph>
      <strong>Deseja aprovar o vendedor?</strong>
    </a-typography-paragraph>
  </a-modal>
</template>

<script lang="ts">
import {
  defineComponent,
  PropType,
  ref,
  toRefs,
  watch,
} from "@vue/runtime-core";
import { SellerItem } from "@/types/Seller.types";

export default defineComponent({
  name: "ApproveSellerModal",
  props: {
    seller: {
      type: Object as PropType<SellerItem>,
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
    const { seller, visible, loading } = toRefs(props);

    function handleApprove(seller?: SellerItem) {
      emit("approve", seller);
    }

    function handleCancel() {
      emit("cancel");
    }

    watch(visible, (newVal) => {
      show.value = newVal;
    });

    return {
      seller,
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