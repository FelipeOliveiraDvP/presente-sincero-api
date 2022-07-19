<template>
  <h1>Cadastro vendedor</h1>
  <!-- <b-container class="page my-4">
    <div class="p-4 my-4 d-flex justify-content-center align-items-center h-100">
      <div class="p-3 border rounded" style="width: 400px">
        <h3>Cadastre-se</h3>
        <p>
          Realize seu cadastro abaixo para ter acesso a vantagens exclusivas
        </p>

        <b-form @submit.stop.prevent="onSubmit">
          <b-form-group label="Nome completo" label-for="name">
            <b-form-input id="name" name="name" v-model="$v.form.name.$model" :state="validateState('name')">
            </b-form-input>

            <b-form-invalid-feedback v-if="!$v.form.name.required">Campo obrigatório</b-form-invalid-feedback>
          </b-form-group>

          <b-form-group label="WhatsApp" label-for="whatsapp">
            <b-form-input id="whatsapp" name="whatsapp" v-model="$v.form.whatsapp.$model"
              :state="validateState('whatsapp')"></b-form-input>

            <b-form-invalid-feedback v-if="!$v.form.whatsapp.required">Campo obrigatório</b-form-invalid-feedback>
            <b-form-invalid-feedback v-if="!$v.form.whatsapp.numeric">Digite somente números</b-form-invalid-feedback>
            <b-form-invalid-feedback v-if="!$v.form.whatsapp.minLength">Informe um número de WhatsApp válido
            </b-form-invalid-feedback>
          </b-form-group>

          <b-form-group label="E-mail" label-for="email">
            <b-form-input id="email" name="email" v-model="$v.form.email.$model" :state="validateState('email')">
            </b-form-input>

            <b-form-invalid-feedback v-if="!$v.form.email.required">Campo obrigatório</b-form-invalid-feedback>
            <b-form-invalid-feedback v-if="!$v.form.email.email">Informe um e-mail válido</b-form-invalid-feedback>
          </b-form-group>

          <b-form-group label="Senha" label-for="password">
            <b-form-input id="password" name="password" type="password" v-model="$v.form.password.$model"
              :state="validateState('password')"></b-form-input>

            <b-form-invalid-feedback v-if="!$v.form.password.required">Campo obrigatório</b-form-invalid-feedback>
            <b-form-invalid-feedback v-if="!$v.form.password.minLength">Senha deve ter 8 caracteres
            </b-form-invalid-feedback>
          </b-form-group>

          <b-form-group label="Confirmar Senha" label-for="password_confirmation">
            <b-form-input id="password_confirmation" name="password_confirmation" type="password"
              v-model="$v.form.password_confirmation.$model" :state="validateState('password_confirmation')">
            </b-form-input>

            <b-form-invalid-feedback v-if="!$v.form.password_confirmation.required">Campo obrigatório
            </b-form-invalid-feedback>
            <b-form-invalid-feedback v-if="!$v.form.password_confirmation.sameAsPassword">As senhas precisam ser iguais
            </b-form-invalid-feedback>
          </b-form-group>

          <div>
            <b-button type="submit" block variant="primary" class="w-100 mt-4" :disabled="loading">Entrar</b-button>
          </div>

          <div class="my-2 d-flex justify-content-between">
            <router-link to="/recuperar-senha">
              Esqueci minha senha
            </router-link>

            <router-link to="/cadastre-se"> Criar nova conta </router-link>
          </div>
        </b-form>
      </div>
    </div>
  </b-container> -->
</template>

<script>


import { mapActions } from "vuex";
import { register } from "../services/auth";

export default {
  name: "Register",

  data() {
    return {
      loading: false,
      form: {
        name: "",
        whatsapp: "",
        email: "",
        password: "",
        password_confirmation: "",
      },
    };
  },

  methods: {
    ...mapActions({
      signIn: "auth/login",
    }),

    async onSubmit() {
      try {
        this.loading = true;

        const result = await register(this.form);

        this.signIn(result);
      } catch (error) {
        console.error(error);
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>

<style>
</style>