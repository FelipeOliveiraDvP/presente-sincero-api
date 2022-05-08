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
        <h3>Cadastre-se</h3>
        <p class="text-muted">
          Realize seu cadastro abaixo para ter acesso a vantagens exclusivas
        </p>
        <b-form @submit="onSubmit">
          <div class="form-floating mb-3">
            <input
              v-model="form.name"
              type="text"
              class="form-control"
              id="name"
              placeholder="name"
              required
            />
            <label for="name">Nome completo</label>
          </div>
          <div class="form-floating mb-3">
            <input
              v-model="form.whatsapp"
              type="text"
              class="form-control"
              id="whatsapp"
              placeholder="whatsapp"
              required
            />
            <label for="whatsapp">WhatsApp</label>
          </div>
          <div class="form-floating mb-3">
            <input
              v-model="form.email"
              type="email"
              class="form-control"
              id="email"
              placeholder="email"
              required
            />
            <label for="email">E-mail</label>
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
          <div class="form-floating mb-3">
            <input
              v-model="form.confirmPassword"
              type="password"
              class="form-control"
              id="confirmPassword"
              placeholder="********"
              required
            />
            <label for="confirmPassword">Confirmar senha</label>
          </div>

          <div>
            <b-button type="submit" block variant="primary" class="w-100"
              >Criar conta</b-button
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
  name: "Register",
  data() {
    return {
      form: {
        name: "",
        whatsapp: "",
        email: "",
        password: "",
        confirmPassword: "",
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