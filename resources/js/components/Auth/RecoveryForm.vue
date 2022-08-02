<template>
  <a-card title="Recuperar senha" style="width: 400px">
    <a-form
      :model="formState"
      name="recovery_form"
      layout="vertical"
      autocomplete="off"
      @finish="handleFinish"
    >
      <a-form-item
        label="WhatsApp ou E-mail"
        name="user"
        :rules="[{ required: true, message: 'Campo obrigatório!' }]"
      >
        <a-input v-model:value="formState.user" />
      </a-form-item>

      <a-form-item>
        <a-button :loading="loading" type="primary" html-type="submit" block
          >Recuperar senha</a-button
        >
      </a-form-item>

      <a-form-item>
        <router-link to="/login"> Ir para o login </router-link>
      </a-form-item>
    </a-form>
  </a-card>
</template>

<script lang="ts">
import { AuthRequest } from "@/types/Auth.types";
import { reactive, toRefs } from "@vue/reactivity";
import { defineComponent } from "@vue/runtime-core";

export default defineComponent({
  name: "RecoveryForm",
  emits: ["onFinish"],
  props: {
    loading: {
      type: Boolean,
    },
  },
  setup(props, { emit }) {
    const { loading } = toRefs(props);
    const formState = reactive<AuthRequest>({
      user: "",
    });

    function handleFinish(values: AuthRequest) {
      emit("onFinish", values);
    }

    return {
      formState,
      loading,
      handleFinish,
    };
  },
});
</script>
