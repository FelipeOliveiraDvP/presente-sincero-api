<template>
  <a-card title="Criar nova conta" style="width: 400px">
    <a-form
      ref="formRef"
      :model="formState"
      name="register_form"
      layout="vertical"
      autocomplete="off"
      @finish="handleFinish"
    >
      <a-form-item
        label="Nome completo"
        name="name"
        :rules="[{ required: true, message: 'Campo obrigatório!' }]"
      >
        <a-input v-model:value="formState.name" />
      </a-form-item>

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
        label="Nome de vendedor"
        name="username"
        help="Esta vai ser a sua marca no site"
        :rules="[{ required: true, message: 'Campo obrigatório!' }]"
      >
        <a-input v-model:value="formState.username" />
      </a-form-item>

      <a-form-item
        label="E-mail"
        name="email"
        :rules="[
          { required: true, message: 'Campo obrigatório!' },
          { type: 'email', message: 'Informe um e-mail válido' },
        ]"
      >
        <a-input v-model:value="formState.email" />
      </a-form-item>

      <a-form-item
        label="Senha"
        name="password"
        :rules="[{ validator: password, trigger: 'change' }]"
      >
        <a-input-password v-model:value="formState.password" />
      </a-form-item>

      <a-form-item
        label="Confirmar senha"
        name="password_confirmation"
        :rules="[{ validator: confirmPassword, trigger: 'change' }]"
      >
        <a-input-password v-model:value="formState.password_confirmation" />
      </a-form-item>

      <a-form-item>
        <a-button :loading="loading" type="primary" html-type="submit" block
          >Criar minha conta</a-button
        >
      </a-form-item>

      <a-form-item>
        <router-link to="/login">
          Já tem uma conta? Clique aqui para acessar
        </router-link>
      </a-form-item>
    </a-form>
  </a-card>
</template>

<script lang="ts">
import { AuthRegisterRequest } from "@/types/Auth.types";
import { reactive, ref, toRefs } from "@vue/reactivity";
import { defineComponent } from "@vue/runtime-core";
import type { Rule } from "ant-design-vue/lib/form";
import type { FormInstance } from "ant-design-vue";

export default defineComponent({
  name: "RegisterForm",
  emits: ["onFinish"],
  props: {
    loading: {
      type: Boolean,
    },
  },
  setup(props, { emit }) {
    const { loading } = toRefs(props);
    const formRef = ref<FormInstance>();
    const formState = reactive<AuthRegisterRequest>({
      name: "",
      whatsapp: "",
      username: "",
      email: "",
      password: "",
      password_confirmation: "",
    });

    let password = async (_rule: Rule, value: string) => {
      if (value === "") {
        return Promise.reject("Campo obrigatório!");
      } else {
        if (formState.password_confirmation !== "") {
          // @ts-ignore
          formRef?.value.validateFields("password_confirmation");
        }
        return Promise.resolve();
      }
    };

    let confirmPassword = async (_rule: Rule, value: string) => {
      if (value === "") {
        return Promise.reject("Confirme a senha");
      } else if (value !== formState.password) {
        return Promise.reject("As senhas não são iguais.");
      } else {
        return Promise.resolve();
      }
    };

    function handleFinish(values: AuthRegisterRequest) {
      emit("onFinish", values);
    }

    return {
      formRef,
      formState,
      loading,
      password,
      confirmPassword,
      handleFinish,
    };
  },
});
</script>
