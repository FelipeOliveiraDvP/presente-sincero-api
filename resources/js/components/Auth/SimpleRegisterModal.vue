<template>
  <b-modal
    id="simple-register"
    title="Finalizar compra"
    :ok-title="newAccount ? 'Criar conta' : 'Acessar minha conta'"
    :ok-disabled="loading"
    ok-only
    hide-header-close
    @ok="handleOk"
    @hidden="handleClose"
  >
    <p v-if="newAccount">
      Caso você já tenha uma conta, informe seu WhatsApp abaixo para continuar
    </p>
    <div v-else>
      <p>
        Por favor, nos informe seu nome e WhatsApp abaixo para finalizar a
        compra.
      </p>
    </div>

    <b-form ref="form" class="mb-3" @submit.stop.prevent="handleSubmit">
      <b-form-group v-if="newAccount" class="mb-4">
        <b-form-input
          id="name"
          v-model="$v.form.name.$model"
          :state="!newAccount && form.name === ''"
          placeholder="Nome completo"
        />

        <b-form-invalid-feedback v-if="!newAccount && form.name === ''"
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
    <p>
      <strong>IMPORTANTE:</strong> Tenha certeza de nos informar o WhatsApp
      correto, pois é através dele que enviaremos seu comprovante.
    </p>
  </b-modal>
</template>

<script>
import { mapActions } from "vuex";
import { validationMixin } from "vuelidate";
import { required, minLength, numeric } from "vuelidate/lib/validators";

import { simpleLogin } from "@/services/auth";

export default {
  name: "SimpleRegisterModal",
  mixins: [validationMixin],
  data() {
    return {
      loading: false,
      newAccount: false,
      form: {
        name: "",
        whatsapp: "",
      },
    };
  },
  validations: {
    form: {
      name: {},
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

        const result = await simpleLogin({
          ...this.form,
          new_account: this.newAccount,
        });

        if (result.new_account === true) {
          this.newAccount = true;

          this.$toasted.show(result.message, {
            type: "success",
            theme: "toasted-primary",
            position: "top-right",
            duration: 3000,
          });

          this.loading = false;
        } else {
          this.signIn(result);

          this.$bvModal.hide("simple-register");

          this.$emit("onsuccess");
        }
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