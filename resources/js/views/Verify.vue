<template>
  <h1>Verificar código</h1>
  <!-- <b-container class="page my-4">
    <div
      class="p-4 my-4 d-flex justify-content-center align-items-center h-100"
    >
      <div class="p-3 border rounded" style="width: 400px">
        <h3>Recuperação de senha</h3>
        <p class="text-muted">Digite o código enviado para o seu WhatsApp</p>
        <b-form @submit.stop.prevent="onSubmit">
          <b-form-group label="Código de verificação" label-for="code">
            <b-form-input
              id="code"
              name="code"
              v-model="$v.form.code.$model"
              :state="validateState('code')"
            ></b-form-input>

            <b-form-invalid-feedback v-if="!$v.form.code.required"
              >Campo obrigatório</b-form-invalid-feedback
            >
            <b-form-invalid-feedback v-if="!$v.form.code.minLength"
              >Digite um código válido</b-form-invalid-feedback
            >
          </b-form-group>

          <div>
            <b-button
              type="submit"
              block
              variant="primary"
              class="w-100 mt-4"
              :disabled="loading"
              >Verificar código</b-button
            >
          </div>

          <div class="my-2 d-flex justify-content-between">
            <router-link to="/recuperar-senha">
              Solicitar um novo código
            </router-link>
          </div>
        </b-form>
      </div>
    </div>
  </b-container> -->
</template>

<script>
import { verify } from "../services/auth";

export default {
  name: "Verify",
  data() {
    return {
      loading: false,
      form: {
        code: "",
      },
    };
  },
  methods: {
    async onSubmit() {
      try {
        this.loading = true;

        const result = await verify(this.form.code);

        console.log(result);

        this.$router.push({ name: "reset", params: { code: this.form.code } });
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