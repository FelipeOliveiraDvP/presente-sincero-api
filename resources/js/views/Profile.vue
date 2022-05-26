<template>
  <b-container class="page my-4">
    <div class="p-4 my-4 border">
      <h2>Minha conta</h2>
      <b-row>
        <b-col md="6" lg="4" class="text-center mb-4">
          <b-avatar
            variant="info"
            src="https://placekitten.com/300/300"
            style="width: 300px; height: 300px"
          ></b-avatar>
        </b-col>
        <b-col md="6" lg="8">
          <b-form @submit="onUpdateProfile">
            <div class="form-floating mb-3">
              <input
                v-model="updateProfile.name"
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
                v-model="updateProfile.whatsapp"
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
                v-model="updateProfile.email"
                type="email"
                class="form-control"
                id="email"
                placeholder="email"
                required
              />
              <label for="email">E-mail</label>
            </div>

            <div>
              <b-button type="submit" block variant="primary" class="w-100 mb-4"
                >Salvar informações</b-button
              >
            </div>
          </b-form>

          <b-form @submit="onChangePassword">
            <div class="form-floating mb-3">
              <input
                v-model="changePassword.password"
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
                v-model="changePassword.confirmPassword"
                type="password"
                class="form-control"
                id="confirmPassword"
                placeholder="********"
                required
              />
              <label for="confirmPassword">Confirmar senha</label>
            </div>

            <div>
              <b-button type="submit" block variant="danger" class="w-100"
                >Alterar senha</b-button
              >
            </div>
          </b-form>
        </b-col>
      </b-row>
    </div>
  </b-container>
</template>

<script>
export default {
  name: "Profile",
  data() {
    return {
      updateProfile: {
        name: "",
        whatsapp: "",
        email: "",
      },
      changePassword: {
        password: "",
        confirmPassword: "",
      },
      validate: false,
    };
  },
  methods: {
    onUpdateProfile(e) {
      e.preventDefault();

      console.log(this.updateProfile);
    },
    onChangePassword(e) {
      e.preventDefault();

      console.log(this.changePassword);
    },
  },
};
</script>

<style>
</style>