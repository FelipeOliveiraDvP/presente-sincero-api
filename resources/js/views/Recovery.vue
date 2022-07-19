<template>
  <h1>Recuperar senha</h1>
  <!-- <b-container class="page my-4">
    <div class="p-4 my-4 d-flex justify-content-center align-items-center h-100">
      <div class="p-3 border rounded" style="width: 400px">
        <h3>Recuperação de senha</h3>
        <p class="text-muted">
          Informe seu WhatsApp ou e-mail para recuperar sua senha
        </p>
        <b-form @submit.stop.prevent="onSubmit">
          <b-form-group label="WhatsApp ou E-mail" label-for="user">
            <b-form-input id="user" name="user" v-model="$v.form.user.$model" :state="validateState('user')">
            </b-form-input>

            <b-form-invalid-feedback v-if="!$v.form.user.required">Campo obrigatório</b-form-invalid-feedback>
          </b-form-group>

          <div>
            <b-button type="submit" block variant="primary" class="w-100 mt-4" :disabled="loading">Recuperar senha
            </b-button>
          </div>

          <div class="my-2 d-flex justify-content-between">
            <router-link to="/login"> Ir para o login </router-link>
          </div>
        </b-form>
      </div>
    </div>
  </b-container> -->
</template>

<script>
import { forgot } from "../services/auth";

export default {
  name: "Recovery",
  data() {
    return {
      loading: false,
      form: {
        user: "",
      },
    };
  },
  methods: {
    async onSubmit() {
      try {
        this.loading = true;

        const result = await forgot(this.form);

        console.log(result);

        this.$router.push({ name: "verify" });
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