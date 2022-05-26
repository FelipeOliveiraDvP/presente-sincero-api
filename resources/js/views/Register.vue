<template>
  <b-container class="page my-4">
    <div
      class="p-4 my-4 d-flex justify-content-center align-items-center h-100"
    >
      <div class="p-3 border rounded" style="width: 400px">
        <h3>Cadastre-se</h3>
        <p>
          Realize seu cadastro abaixo para ter acesso a vantagens exclusivas
        </p>

        <b-form @submit.stop.prevent="onSubmit">
          <b-form-group label="Nome completo" label-for="name">
            <b-form-input
              id="name"
              name="name"
              v-model="$v.form.name.$model"
              :state="validateState('name')"
            ></b-form-input>

            <b-form-invalid-feedback v-if="!$v.form.name.required"
              >Campo obrigatório</b-form-invalid-feedback
            >
          </b-form-group>

          <b-form-group label="WhatsApp" label-for="whatsapp">
            <b-form-input
              id="whatsapp"
              name="whatsapp"
              v-model="$v.form.whatsapp.$model"
              :state="validateState('whatsapp')"
            ></b-form-input>

            <b-form-invalid-feedback v-if="!$v.form.whatsapp.required"
              >Campo obrigatório</b-form-invalid-feedback
            >
            <b-form-invalid-feedback v-if="!$v.form.whatsapp.numeric"
              >Digite somente números</b-form-invalid-feedback
            >
            <b-form-invalid-feedback v-if="!$v.form.whatsapp.minLength"
              >Informe um número de WhatsApp válido</b-form-invalid-feedback
            >
          </b-form-group>

          <b-form-group label="E-mail" label-for="email">
            <b-form-input
              id="email"
              name="email"
              v-model="$v.form.email.$model"
              :state="validateState('email')"
            ></b-form-input>

            <b-form-invalid-feedback v-if="!$v.form.email.required"
              >Campo obrigatório</b-form-invalid-feedback
            >
            <b-form-invalid-feedback v-if="!$v.form.email.email"
              >Informe um e-mail válido</b-form-invalid-feedback
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

          <b-form-group
            label="Confirmar Senha"
            label-for="password_confirmation"
          >
            <b-form-input
              id="password_confirmation"
              name="password_confirmation"
              type="password"
              v-model="$v.form.password_confirmation.$model"
              :state="validateState('password_confirmation')"
            ></b-form-input>

            <b-form-invalid-feedback
              v-if="!$v.form.password_confirmation.required"
              >Campo obrigatório</b-form-invalid-feedback
            >
            <b-form-invalid-feedback
              v-if="!$v.form.password_confirmation.sameAsPassword"
              >As senhas precisam ser iguais</b-form-invalid-feedback
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

        <!-- <b-form @submit="onSubmit">
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
        </b-form> -->
      </div>
    </div>
  </b-container>
</template>

<script>
import { validationMixin } from "vuelidate";
import {
  required,
  minLength,
  sameAs,
  email,
  numeric,
} from "vuelidate/lib/validators";
import { mapActions } from "vuex";
import { register } from "../services/auth";

export default {
  name: "Register",
  mixins: [validationMixin],
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
  validations: {
    form: {
      name: { required },
      whatsapp: { required, numeric, minLength: minLength(10) },
      email: { required, email },
      password: { required, minLength: minLength(8) },
      password_confirmation: { required, sameAsPassword: sameAs("password") },
    },
  },
  methods: {
    ...mapActions({
      signIn: "auth/login",
    }),
    validateState(field) {
      const { $dirty, $error } = this.$v.form[field];

      return $dirty ? !$error : null;
    },
    async onSubmit() {
      try {
        this.loading = true;
        this.$v.form.$touch();

        if (this.$v.form.$anyError) {
          return;
        }

        const result = await register(this.form);

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