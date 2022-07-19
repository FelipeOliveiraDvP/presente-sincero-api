<template>
  <h1>Minha conta</h1>
  <!-- <b-container class="page my-4">
    <div class="p-4 my-4 border rounded">
      <h2>Minha conta</h2>
      <b-row>
        <b-col md="6" class="mb-4">
          <b-form @submit.stop.prevent="onUpdateProfile">
            <b-form-group label="Nome completo" label-for="name">
              <b-form-input id="name" name="name" v-model="$v.updateProfile.name.$model"
                :state="validateState($v.updateProfile, 'name')"></b-form-input>

              <b-form-invalid-feedback v-if="!$v.updateProfile.name.required">Campo obrigatório
              </b-form-invalid-feedback>
            </b-form-group>

            <b-form-group label="WhatsApp" label-for="whatsapp">
              <b-form-input id="whatsapp" name="whatsapp" v-model="$v.updateProfile.whatsapp.$model"
                :state="validateState($v.updateProfile, 'whatsapp')"></b-form-input>

              <b-form-invalid-feedback v-if="!$v.updateProfile.whatsapp.required">Campo obrigatório
              </b-form-invalid-feedback>
              <b-form-invalid-feedback v-if="!$v.updateProfile.whatsapp.numeric">Digite somente números
              </b-form-invalid-feedback>
              <b-form-invalid-feedback v-if="!$v.updateProfile.whatsapp.minLength">Informe um número de WhatsApp válido
              </b-form-invalid-feedback>
            </b-form-group>

            <b-form-group label="E-mail" label-for="email">
              <b-form-input id="email" name="email" v-model="$v.updateProfile.email.$model"
                :state="validateState($v.updateProfile, 'email')"></b-form-input>

              <b-form-invalid-feedback v-if="!$v.updateProfile.email.required">Campo obrigatório
              </b-form-invalid-feedback>
              <b-form-invalid-feedback v-if="!$v.updateProfile.email.email">Informe um e-mail válido
              </b-form-invalid-feedback>
            </b-form-group>

            <div>
              <b-button type="submit" block variant="primary" class="w-100 mt-4" :disabled="loading">Salvar informações
              </b-button>
            </div>
          </b-form>
        </b-col>
        <b-col md="6">
          <b-form @submit.stop.prevent="onChangePassword">
            <b-form-group label="Senha" label-for="password">
              <b-form-input id="password" name="password" type="password" v-model="$v.changePassword.password.$model"
                :state="validateState($v.changePassword, 'password')"></b-form-input>

              <b-form-invalid-feedback v-if="!$v.changePassword.password.required">Campo obrigatório
              </b-form-invalid-feedback>
              <b-form-invalid-feedback v-if="!$v.changePassword.password.minLength">Senha deve ter 8 caracteres
              </b-form-invalid-feedback>
            </b-form-group>

            <b-form-group label="Confirmar Senha" label-for="password_confirmation">
              <b-form-input id="password_confirmation" name="password_confirmation" type="password"
                v-model="$v.changePassword.password_confirmation.$model" :state="
                  validateState($v.changePassword, 'password_confirmation')
                "></b-form-input>

              <b-form-invalid-feedback v-if="!$v.changePassword.password_confirmation.required">Campo obrigatório
              </b-form-invalid-feedback>
              <b-form-invalid-feedback v-if="!$v.changePassword.password_confirmation.sameAsPassword">As senhas precisam
                ser iguais</b-form-invalid-feedback>
            </b-form-group>

            <div>
              <b-button type="submit" block variant="outline-danger" class="w-100 mt-4" :disabled="loading">Alterar
                senha</b-button>
            </div>
          </b-form>
        </b-col>
      </b-row>
    </div>
  </b-container> -->
</template>

<script>
import { mapActions } from "vuex";
import { getProfile, editProfile } from "@/services/auth";

export default {
  name: "Profile",

  data() {
    return {
      loading: false,
      updateProfile: {
        name: "",
        whatsapp: "",
        email: "",
      },
      changePassword: {
        password: "",
        password_confirmation: "",
      },
    };
  },
  mounted() {
    this.getProfileData();
  },
  methods: {
    ...mapActions({
      refreshUserData: "auth/updateUser",
    }),
    async onUpdateProfile() {
      try {
        this.loading = true;

        const result = await editProfile(this.updateProfile);

        this.updateProfile = { ...result.user };
        this.refreshUserData({ ...result.user });

        console.log(result);
      } catch (error) {
        console.error(error);
      } finally {
        this.loading = false;
      }
    },
    async onChangePassword() {
      try {
        this.loading = true;

        const result = await editProfile(this.changePassword);

        this.changePassword = {
          password: "",
          password_confirmation: "",
        };

        console.log(result);
      } catch (error) {
        console.error(error);
      } finally {
        this.loading = false;
      }
    },
    async getProfileData() {
      try {
        this.loading = true;

        const result = await getProfile();

        this.updateProfile = { ...result.user };
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