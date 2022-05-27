<template>
  <b-modal
    id="bank-account-modal"
    ref="modal"
    :title="modalTitle"
    ok-title="Salvar"
    cancel-title="Cancelar"
    :ok-disabled="loading"
    @show="handleReset"
    @hidden="handleReset"
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

      <b-form-group label="Nome" label-for="name">
        <b-form-input
          id="name"
          v-model="$v.bank_account.name.$model"
          :state="validateState('name')"
        />
      </b-form-group>

      <b-row v-if="bank_account.type === 'BANK'">
        <b-col md="6">
          <b-form-group label="Conta" label-for="cc">
            <b-form-input
              id="cc"
              v-model="$v.bank_account.cc.$model"
              :state="validateState('cc')"
            />
          </b-form-group>
        </b-col>
        <b-col md="4">
          <b-form-group label="Agência" label-for="agencia">
            <b-form-input
              id="agencia"
              v-model="$v.bank_account.agencia.$model"
              :state="validateState('agencia')"
            />
          </b-form-group>
        </b-col>
        <b-col md="2">
          <b-form-group label="DV" label-for="dv">
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
        label-for="chave"
      >
        <b-form-input
          id="chave"
          name="chave"
          v-model="$v.bank_account.chave.$model"
          :state="validateState('chave')"
        />
      </b-form-group>
    </b-form>
  </b-modal>
</template>

<script>
import { validationMixin } from "vuelidate";
import {
  required,
  numeric,
  requiredUnless,
  minLength,
} from "vuelidate/lib/validators";

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
        agencia: "",
        dv: "",
        chave: "",
      },
      account_types: [
        { value: "PIX", text: "Pagar com PIX" },
        { value: "BANK", text: "Conta bancária" },
      ],
    };
  },
  watch: {
    account: function (newVal, oldVal) {
      this.bank_account = { ...newVal };
    },
  },
  validations() {
    return {
      bank_account: {
        name: { required },
        cc: {
          requiredIfBank: requiredUnless(this.bank_account.type === "BANK"),
        },
        agencia: {
          requiredIfBank: requiredUnless(this.bank_account.type === "BANK"),
        },
        dv: {
          requiredIfBank: requiredUnless(this.bank_account.type === "BANK"),
        },
        chave: {
          minLength: minLength(10),
          requiredIfPix: requiredUnless(this.bank_account.type === "PIX"),
        },
      },
    };
  },
  mounted() {
    if (this.account !== undefined) {
      this.bank_account = { ...this.account };
    }
  },
  computed: {
    modalTitle() {
      return this.account !== undefined ? "Editar conta" : "Nova conta";
    },
  },
  methods: {
    handleReset() {
      const reset = {
        type: "PIX",
        name: "",
        cc: "",
        agencia: "",
        dv: "",
        chave: "",
      };

      this.bank_account = { ...reset };
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

      // if (this.$v.$anyError) return;

      this.onSubmit(this.bank_account);
    },
  },
};
</script>

<style>
</style>