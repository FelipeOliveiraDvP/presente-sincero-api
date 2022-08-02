<template>
  <container>
    <a-page-header title="Minha conta" />

    <a-form
      ref="formRef"
      :model="formState"
      name="profile_form"
      layout="vertical"
      autocomplete="off"
      @finish="handleFinish"
    >
      <a-row :gutter="[16, 16]">
        <a-col :xs="24" :md="12" :lg="8">
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
              { required: true, message: 'Campo obrigatório' },
              { type: 'email', message: 'Informe um e-mail válido' },
            ]"
          >
            <a-input v-model:value="formState.email" />
          </a-form-item>
        </a-col>
        <a-col :xs="24" :md="12" :lg="8">
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
        </a-col>
        <a-col :xs="24" :md="12" :lg="8"></a-col>
      </a-row>

      <a-form-item>
        <a-button :loading="loading" type="primary" html-type="submit"
          >Salvar alterações</a-button
        >
      </a-form-item>
    </a-form>
  </container>
</template>

<script lang="ts">
import Container from "@/components/_commons/Container.vue";
import { editProfile } from "@/services/auth";
import { useAuthStore } from "@/store/auth";
import { ErrorResponse } from "@/types/api.types";
import { AuthProfileRequest, AuthUser } from "@/types/Auth.types";
import { getErrorMessage } from "@/utils/handleError";
import { defineComponent, reactive, ref } from "@vue/runtime-core";
import { FormInstance, notification } from "ant-design-vue";
import { Rule } from "ant-design-vue/lib/form";
import { storeToRefs } from "pinia";

export default defineComponent({
  components: { Container },
  name: "Profile",
  setup() {
    const store = useAuthStore();

    const loading = ref<boolean>(false);
    const formRef = ref<FormInstance>();
    const { user } = storeToRefs(store);

    const formState = reactive<AuthProfileRequest>({
      name: "",
      whatsapp: "",
      email: "",
      username: "",
      password: "",
      password_confirmation: "",
    });

    if (user.value !== null) {
      const { name, whatsapp, email, username } = user.value as AuthUser;

      formState.name = name || "";
      formState.whatsapp = whatsapp || "";
      formState.email = email || "";
      formState.username = username || "";
    }

    let password = async (_rule: Rule) => {
      if (formState.password_confirmation !== "") {
        // @ts-ignore
        formRef?.value.validateFields("password_confirmation");
      }
      return Promise.resolve();
    };

    let confirmPassword = async (_rule: Rule, value: string) => {
      if (formState.password !== "" && value === "") {
        return Promise.reject("Confirme a senha");
      } else if (value !== formState.password) {
        return Promise.reject("As senhas não são iguais.");
      } else {
        return Promise.resolve();
      }
    };

    async function handleFinish(values: AuthProfileRequest) {
      try {
        loading.value = true;
        const result = await editProfile(values);

        notification.success({
          message: result.message,
        });

        store.updateUser(result.user);
      } catch (error: unknown) {
        notification.error({
          message: getErrorMessage(error),
        });
      } finally {
        loading.value = false;
      }
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

<style>
</style>