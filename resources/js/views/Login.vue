<template>
  <b-container class="page my-4">
    <div
      class="p-4 my-4 d-flex justify-content-center align-items-center h-100"
    >
      <div class="p-3 border rounded" style="width: 400px">
        <h3>Acessar minha conta</h3>
        <p>Informe seus dados de acesso para acessar sua área exclusiva</p>

        <b-form @submit.stop.prevent="onSubmit">
          <b-form-group label="WhatsApp ou E-mail" label-for="user">
            <b-form-input
              id="user"
              name="user"
              v-model="$v.form.user.$model"
              :state="validateState('user')"
            ></b-form-input>

            <b-form-invalid-feedback v-if="!$v.form.user.required"
              >Campo obrigatório</b-form-invalid-feedback
            >
          </b-form-group>

          <b-form-group label="Senha" label-for="password">
            <b-form-input
              id="password"
              name="password"
              type="password"
              v-model="$v.form.password.$model"
              :state="validateState('password')"
            ></b-form-input>

            <b-form-invalid-feedback v-if="!$v.form.password.required"
              >Campo obrigatório</b-form-invalid-feedback
            >
            <b-form-invalid-feedback v-if="!$v.form.password.minLength"
              >Senha deve ter 8 caracteres</b-form-invalid-feedback
            >
          </b-form-group>
          <div>
            <b-button
              type="submit"
              block
              variant="primary"
              class="w-100 mt-4"
              :disabled="loading"
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
import { validationMixin } from "vuelidate";
import { required, minLength } from "vuelidate/lib/validators";
import { mapActions } from "vuex";
import { login } from "../services/auth";

export default {
  name: "Login",
  mixins: [validationMixin],
  data() {
    return {
      loading: false,
      form: {
        user: "",
        password: "",
      },
    };
  },
  validations: {
    form: {
      user: { required },
      password: { required, minLength: minLength(8) },
    },
  },
  methods: {
    ...mapActions({
      signIn: "auth/login",
    }),
    validateState(user) {
      const { $dirty, $error } = this.$v.form[user];

      return $dirty ? !$error : null;
    },
    async onSubmit() {
      try {
        this.loading = true;
        this.$v.form.$touch();

        if (this.$v.form.$anyError) {
          return;
        }

        const result = await login(this.form);

        this.signIn(result);
      } catch (error) {
        this.$toasted.show(error.message, {
          type: "error",
          theme: "toasted-primary",
          position: "top-right",
          duration: 3000,
        });
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>

<style>
</style>