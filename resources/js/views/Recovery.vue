<template>
  <b-container class="page my-4">
    <div
      class="
        bg-white
        p-4
        my-4
        border
        d-flex
        justify-content-center
        align-items-center
        h-100
      "
    >
      <div class="p-3 border" style="width: 400px">
        <h3>Recuperação de senha</h3>
        <p class="text-muted">
          Informe seu WhatsApp ou e-mail para recuperar sua senha
        </p>
        <b-form @submit="onSubmit">
          <div class="form-floating mb-3">
            <input
              v-model="form.identifier"
              type="text"
              class="form-control"
              id="identifier"
              placeholder="name@example.com"
              required
            />
            <label for="identifier">E-mail ou WhatsApp</label>
          </div>

          <div>
            <b-button type="submit" block variant="primary" class="w-100"
              >Recuperar senha</b-button
            >
          </div>

          <div class="my-2 d-flex justify-content-between">
            <router-link to="/login"> Ir para o login </router-link>
          </div>
        </b-form>
      </div>
    </div>
  </b-container>
</template>

<script>
export default {
  name: "Recovery",
  data() {
    return {
      form: {
        identifier: "",
      },
    };
  },
  methods: {
    onSubmit(e) {
      e.preventDefault();

      console.log(this.form);
    },
  },
};
</script>

<style>
</style>