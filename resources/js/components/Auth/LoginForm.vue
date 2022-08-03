<template>
  <a-card title="Faça login para começar" style="width: 400px">
    <a-form
      :model="formState"
      name="login_form"
      layout="vertical"
      autocomplete="off"
      @finish="handleFinish"
    >
      <a-form-item
        label="Nome de usuário ou WhatsApp"
        name="user"
        :rules="[{ required: true, message: 'Campo obrigatório!' }]"
      >
        <a-input v-model:value="formState.user" />
      </a-form-item>

      <a-form-item
        label="Senha"
        name="password"
        :rules="[{ required: true, message: 'Campo obrigatório!' }]"
      >
        <a-input-password v-model:value="formState.password" />
      </a-form-item>

      <a-form-item>
        <a-button :loading="loading" type="primary" html-type="submit" block
          >Entrar</a-button
        >
      </a-form-item>

      <a-form-item>
        <router-link to="/recuperar-senha"> Esqueci minha senha </router-link>
      </a-form-item>
    </a-form>
  </a-card>
</template>

<script lang="ts">
import { AuthLoginRequest } from "@/types/Auth.types";
import { reactive, toRefs } from "@vue/reactivity";
import { defineComponent } from "@vue/runtime-core";

export default defineComponent({
  name: "LoginForm",
  emits: ["onFinish"],
  props: {
    loading: {
      type: Boolean,
    },
  },
  setup(props, { emit }) {
    const { loading } = toRefs(props);
    const formState = reactive<AuthLoginRequest>({
      user: "",
      password: "",
    });

    function handleFinish(values: AuthLoginRequest) {
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
