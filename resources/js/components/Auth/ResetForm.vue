<template>
  <a-card title="Criar nova conta" style="width: 400px">
    <a-form
      ref="formRef"
      :model="formState"
      name="reset_form"
      layout="vertical"
      autocomplete="off"
      @finish="handleFinish"
    >
      <a-form-item label="Código de verificação" name="code">
        <a-input v-model:value="formState.code" disabled />
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
          >Alterar senha</a-button
        >
      </a-form-item>

      <a-form-item>
        <router-link to="/login"> Ir para o login </router-link>
      </a-form-item>
    </a-form>
  </a-card>
</template>

<script lang="ts">
import { AuthRegisterRequest, AuthResetRequest } from "@/types/Auth.types";
import { reactive, ref, toRefs } from "@vue/reactivity";
import { defineComponent } from "@vue/runtime-core";
import type { Rule } from "ant-design-vue/lib/form";
import type { FormInstance } from "ant-design-vue";

export default defineComponent({
  name: "ResetForm",
  emits: ["onFinish"],
  props: {
    code: {
      type: String,
      required: true,
    },
    loading: {
      type: Boolean,
    },
  },
  setup(props, { emit }) {
    const { loading, code } = toRefs(props);
    const formRef = ref<FormInstance>();
    const formState = reactive<AuthResetRequest>({
      code: code.value,
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
