<template>
  <h1>Resetar senha</h1>
  <!-- <b-container class="page my-4">
    <div class="p-4 my-4 d-flex justify-content-center align-items-center h-100">
      <div class="p-3 border rounded" style="width: 400px">
        <h3>Cadastrar nova senha</h3>
        <p class="text-muted">
          Cadastre uma nova senha para ter acesso a sua conta
        </p>
        <b-form @submit.stop.prevent="onSubmit">
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
            <b-button type="submit" block variant="primary" class="w-100 mt-4" :disabled="loading">Salvar nova senha
            </b-button>
          </div>

          <div class="my-2 d-flex justify-content-between">
            <router-link to="/recuperar-senha">
              Solicitar um novo código
            </router-link>
            <router-link to="/login"> Ir para o login </router-link>
          </div>
        </b-form>
      </div>
    </div>
  </b-container> -->
</template>

<script>
import { mapActions } from "vuex";
import { reset } from "../services/auth";

export default {
  name: "Reset",
  data() {
    return {
      loading: false,
      form: {
        code: null,
        password: "",
        password_confirmation: "",
      },
    };
  },
  mounted() {
    const { code } = this.$route.params;

    if (code === undefined) {
      this.$router.push({ name: "verify" });
      return;
    }

    this.form.code = code;
  },
  methods: {
    ...mapActions({
      signIn: "auth/login",
    }),
    async onSubmit() {
      try {
        this.loading = true;

        const result = await reset(this.form);

        console.log(result);

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