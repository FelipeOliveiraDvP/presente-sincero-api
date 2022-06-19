<template>
  <b-modal
    id="customer-numbers"
    title="Meus números"
    ok-title="Ver meus números"
    :ok-disabled="loading"
    @ok="handleOk"
    @hidden="handleClose"
  >
    <p>Informe seu WhatsApp abaixo para conferir seus números nesse sorteio</p>
    <b-form ref="form" class="mb-3" @submit.stop.prevent="handleSubmit">
      <b-form-group>
        <b-form-input
          id="whatsapp"
          v-model="$v.whatsapp.$model"
          :state="validateState()"
          placeholder="Informe seu WhatsApp"
        />

        <b-form-invalid-feedback v-if="!$v.whatsapp.required"
          >Campo obrigatório</b-form-invalid-feedback
        >
        <b-form-invalid-feedback v-if="!$v.whatsapp.minLength"
          >WhatsApp inválido</b-form-invalid-feedback
        >
        <b-form-invalid-feedback v-if="!$v.whatsapp.numeric"
          >Informe apenas números</b-form-invalid-feedback
        >
      </b-form-group>
    </b-form>

    <my-loader v-if="loading" />
    <div v-else>
      <b-alert :show="success === false" variant="warning">
        O número informado não é válido ou ainda não está cadastrado.
      </b-alert>

      <b-alert
        :show="success === true && numbers.length === 0"
        variant="warning"
      >
        Você ainda não comprou nenhum número nesse sorteio.
      </b-alert>

      <b-alert :show="success === true && numbers.length > 0" variant="light">
        <h3 class="mb-3">Seus números para esse sorteio</h3>
        <p>{{ numbers.join(", ") }}</p>
      </b-alert>
    </div>

    <p class="text-muted">
      <strong>IMPORTANTE:</strong> Somente vão aparecer os números dos pedidos
      que tiveram o pagamento <strong>confirmado.</strong>
    </p>
  </b-modal>
</template>

<script>
import { validationMixin } from "vuelidate";
import { required, minLength, numeric } from "vuelidate/lib/validators";
import LoaderVue from "@/components/_commons/Loader.vue";

import { getCustomerNumbers } from "@/services/numbers";

export default {
  name: "ContestNumbersModal",
  components: {
    "my-loader": LoaderVue,
  },
  props: {
    contestId: Number,
  },
  mixins: [validationMixin],
  data() {
    return {
      loading: false,
      success: null,
      whatsapp: "",
      numbers: [],
    };
  },
  validations: {
    whatsapp: { required, minLength: minLength(10), numeric },
  },
  methods: {
    async handleSubmit() {
      try {
        this.loading = true;

        this.$v.whatsapp.$touch();

        if (this.$v.whatsapp.$anyError) {
          return;
        }

        const result = await getCustomerNumbers(this.contestId, {
          whatsapp: this.whatsapp,
        });
        const contestNumbers = result.map((r) => JSON.parse(r.numbers));

        this.numbers = [].concat.apply([], contestNumbers);

        this.success = true;
      } catch (error) {
        this.success = false;
      } finally {
        this.loading = false;
      }
    },
    handleOk(e) {
      e.preventDefault();

      this.handleSubmit();
    },
    handleClose() {
      this.whatsapp = "";
      this.success = null;
      this.numbers = [];
    },
    validateState() {
      const { $dirty, $error } = this.$v.whatsapp;

      return $dirty ? !$error : null;
    },
  },
};
</script>

<style>
</style>