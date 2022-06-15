<template>
  <b-modal
    id="simple-register"
    title="Cadastre-se"
    ok-title="Criar conta"
    :ok-disabled="loading"
    ok-only
    hide-header-close
    @ok="handleOk"
    @hidden="handleClose"
  >
    <p>Para continuar, você deve informar seu nome e WhatsApp</p>
    <b-form ref="form" class="mb-3" @submit.stop.prevent="handleSubmit">
      <b-form-group class="mb-4">
        <b-form-input
          id="name"
          v-model="$v.form.name.$model"
          :state="validateState('name')"
          placeholder="Nome completo"
        />

        <b-form-invalid-feedback v-if="!$v.form.name.required"
          >Campo obrigatório</b-form-invalid-feedback
        >
      </b-form-group>
      <b-form-group>
        <b-form-input
          id="whatsapp"
          v-model="$v.form.whatsapp.$model"
          :state="validateState('whatsapp')"
          placeholder="Informe seu WhatsApp"
        />

        <b-form-invalid-feedback v-if="!$v.form.whatsapp.required"
          >Campo obrigatório</b-form-invalid-feedback
        >
        <b-form-invalid-feedback v-if="!$v.form.whatsapp.minLength"
          >WhatsApp inválido</b-form-invalid-feedback
        >
        <b-form-invalid-feedback v-if="!$v.form.whatsapp.numeric"
          >Informe apenas números</b-form-invalid-feedback
        >
      </b-form-group>
    </b-form>
  </b-modal>
</template>

<script>
import { mapActions } from "vuex";
import { register } from "@/services/auth";
import { validationMixin } from "vuelidate";
import { required, minLength, numeric } from "vuelidate/lib/validators";

export default {
  name: "SimpleRegisterModal",
  mixins: [validationMixin],
  data() {
    return {
      loading: false,
      form: {
        name: "",
        whatsapp: "",
      },
    };
  },
  validations: {
    form: {
      name: { required },
      whatsapp: { required, minLength: minLength(10), numeric },
    },
  },
  methods: {
    ...mapActions({
      signIn: "auth/simpleLogin",
    }),
    async handleSubmit() {
      try {
        this.loading = true;

        this.$v.form.$touch();

        if (this.$v.form.$anyError) {
          return;
        }

        const result = await register(this.form);

        this.signIn(result);

        this.$bvModal.hide("simple-register");
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
    handleOk(e) {
      e.preventDefault();

      this.handleSubmit();
    },
    handleClose() {
      this.form = { name: "", whatsapp: "" };
    },
    validateState(field) {
      const { $dirty, $error } = this.$v.form[field];

      return $dirty ? !$error : null;
    },
  },
};
</script>

<style>
</style>