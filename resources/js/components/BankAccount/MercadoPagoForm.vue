<template>
  <a-form
    :model="formState"
    name="mercado_pago_form"
    layout="horizontal"
    autocomplete="off"
    @finish="handleFinish"
  >
    <a-form-item
      label="Token do Mercado Pago"
      name="mp_access_token"
      :rules="[
        { required: true, message: 'Informe o seu token do Mercado Pago' },
      ]"
    >
      <a-input v-model:value="formState.mp_access_token" />
    </a-form-item>

    <a-form-item>
      <a-button
        :loading="loading || loadingProfile"
        type="primary"
        html-type="submit"
        >Salvar</a-button
      >
    </a-form-item>
  </a-form>
</template>

<script lang="ts">
import {
  defineComponent,
  onMounted,
  reactive,
  ref,
  toRefs,
} from "@vue/runtime-core";
import { UpdateMercadoToken } from "@/types/BankAccount.types";
import { getProfile } from "@/services/auth";
import { AuthProfileResponse } from "@/types/Auth.types";

export default defineComponent({
  name: "MercadoPagoForm",
  emits: ["onFinish"],
  props: {
    loading: {
      type: Boolean,
    },
  },
  setup(props, { emit }) {
    const { loading } = toRefs(props);
    const loadingProfile = ref<boolean>(false);
    const formState = reactive<UpdateMercadoToken>({
      mp_access_token: "",
    });

    async function getUserProfile() {
      loadingProfile.value = true;

      const result: AuthProfileResponse = await getProfile();

      formState.mp_access_token = result.user.mp_access_token || "";
      loadingProfile.value = false;
    }

    function handleFinish(values: UpdateMercadoToken) {
      emit("onFinish", values);
    }

    onMounted(async () => {
      await getUserProfile();
    });

    return {
      formState,
      loading,
      loadingProfile,
      handleFinish,
    };
  },
});
</script>

<style>
</style>