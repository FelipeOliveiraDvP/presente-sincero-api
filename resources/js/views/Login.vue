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
        <h3>Acessar minha conta</h3>
        <p class="text-muted">
          Informe seus dados de acesso para acessar sua área exclusiva
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
          <div class="form-floating mb-3">
            <input
              v-model="form.password"
              type="password"
              class="form-control"
              id="password"
              placeholder="********"
              required
            />
            <label for="password">Informe sua senha</label>
          </div>

          <div>
            <b-button type="submit" block variant="primary" class="w-100"
              >Entrar</b-button
            >
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
  </b-container>
</template>

<script>
export default {
  name: "Login",
  data() {
    return {
      form: {
        identifier: "",
        password: "",
      },
      validate: false,
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