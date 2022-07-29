<template>
  <a-modal
    v-model:visible="show"
    :title="
      seller && seller.blocked ? 'Desbloquear vendedor' : 'Bloquear vendedor'
    "
    :ok-text="seller && seller.blocked ? 'Desbloquear' : 'Bloquear'"
    cancel-text="Cancelar"
    destroyOnClose
    :confirmLoading="loading"
    :cancelButtonProps="{ loading: loading }"
    :closable="false"
    :maskClosable="false"
    @ok="handleToggleSeller(seller)"
    @cancel="handleCancel"
  >
    <a-typography-paragraph>
      Deseja {{ seller && seller.blocked ? "desbloquear" : "bloquear" }} o
      vendedor "{{ seller && seller && seller.name }}"?
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
  name: "ToggleSellerModal",
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

    function handleToggleSeller(seller?: SellerItem) {
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
      handleToggleSeller,
      handleCancel,
    };
  },
});
</script>

<style>
</style>