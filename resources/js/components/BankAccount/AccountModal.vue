<template>
  <b-modal
    id="bank-account-modal"
    ref="modal"
    :title="modalTitle"
    ok-title="Salvar"
    cancel-title="Cancelar"
    :ok-disabled="loading"
    @show="openModal"
    @hidden="closeModal"
    @ok="handleOk"
  >
    <b-form ref="form" @submit.stop.prevent="handleSubmit">
      <b-form-group label="Tipo da conta" label-for="type">
        <b-form-select
          class="form-control"
          id="type"
          v-model="bank_account.type"
          :options="account_types"
        />
      </b-form-group>

      <b-form-group
        label="Nome"
        label-for="name"
        invalid-feedback="Campo obrigatório"
      >
        <b-form-input
          id="name"
          v-model="$v.bank_account.name.$model"
          :state="validateState('name')"
        />
      </b-form-group>

      <b-row v-if="bank_account.type === 'BANK'">
        <b-col md="6">
          <b-form-group
            label="Conta"
            label-for="cc"
            invalid-feedback="Campo obrigatório"
          >
            <b-form-input
              id="cc"
              v-model="$v.bank_account.cc.$model"
              :state="validateState('cc')"
            />
          </b-form-group>
        </b-col>
        <b-col md="4">
          <b-form-group
            label="Agência"
            label-for="agency"
            invalid-feedback="Campo obrigatório"
          >
            <b-form-input
              id="agency"
              v-model="$v.bank_account.agency.$model"
              :state="validateState('agency')"
            />
          </b-form-group>
        </b-col>
        <b-col md="2">
          <b-form-group
            label="DV"
            label-for="dv"
            invalid-feedback="Campo obrigatório"
          >
            <b-form-input
              id="dv"
              name="dv"
              v-model="$v.bank_account.dv.$model"
              :state="validateState('dv')"
            />
          </b-form-group>
        </b-col>
      </b-row>

      <b-form-group
        v-if="bank_account.type === 'PIX'"
        label="Chave PIX"
        label-for="key"
        invalid-feedback="Campo obrigatório"
      >
        <b-form-input
          id="key"
          name="key"
          v-model="$v.bank_account.key.$model"
          :state="validateState('key')"
        />
      </b-form-group>
    </b-form>
  </b-modal>
</template>

<script>
import { validationMixin } from "vuelidate";
import { required, minLength } from "vuelidate/lib/validators";

export default {
  name: "BankAccountModal",
  mixins: [validationMixin],
  props: {
    account: Object,
    loading: Boolean,
    onSubmit: Function,
  },
  data() {
    return {
      bank_account: {
        type: "PIX",
        name: "",
        cc: "",
        agency: "",
        dv: "",
        key: "",
        main: false,
      },
      account_types: [
        { value: "PIX", text: "Pagar com PIX" },
        { value: "BANK", text: "Conta bancária" },
      ],
    };
  },
  watch: {
    account(newVal, oldVal) {
      this.bank_account = { ...newVal };
    },
  },
  validations: {
    bank_account: {
      name: { required },
      cc: {
        required,
      },
      agency: {
        required,
      },
      dv: {
        required,
      },
      key: {
        minLength: minLength(10),
        required,
      },
    },
  },
  mounted() {
    if (this.account !== null) {
      this.bank_account = { ...this.account };
    }
  },
  computed: {
    modalTitle() {
      return this.account !== null ? "Editar conta" : "Nova conta";
    },
  },
  methods: {
    handleReset() {
      const reset = {
        type: "PIX",
        name: "",
        cc: "",
        agency: "",
        dv: "",
        key: "",
        main: false,
      };

      this.bank_account = { ...reset };

      this.$emit("reset");
    },
    validateState(field) {
      const { $dirty, $error } = this.$v.bank_account[field];

      return $dirty ? !$error : null;
    },
    handleOk(e) {
      e.preventDefault();

      this.handleSubmit();
    },
    handleSubmit() {
      this.$v.$touch();

      this.onSubmit(this.bank_account);
    },
    openModal(account = null) {
      this.$emit("open", account);
    },
    closeModal() {
      this.$emit("close");
      this.handleReset();
    },
  },
};
</script>

<style>
</style>