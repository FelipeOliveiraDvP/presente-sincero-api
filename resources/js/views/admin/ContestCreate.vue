<template>
  <b-container fluid>
    <div class="p-4">
      <h2>Novo sorteio</h2>

      <b-form @submit.stop.prevent="onSubmit">
        <b-form-group label="Título" label-for="title">
          <b-form-input
            id="title"
            name="title"
            v-model="$v.contest.title.$model"
            :state="validateState('title')"
          ></b-form-input>

          <b-form-invalid-feedback v-if="!$v.contest.title.required"
            >Campo obrigatório</b-form-invalid-feedback
          >
        </b-form-group>

        <b-row>
          <b-col md="6">
            <b-form-group label="Data de início" label-for="start_date">
              <b-form-datepicker
                id="start_date"
                name="start_date"
                v-model="$v.contest.start_date.$model"
                placeholder="Selecione uma data"
                :state="validateState('start_date')"
              ></b-form-datepicker>

              <b-form-invalid-feedback v-if="!$v.contest.start_date.required"
                >Campo obrigatório</b-form-invalid-feedback
              >
            </b-form-group>
          </b-col>
          <b-col md="6">
            <b-form-group label="Data do sorteio" label-for="contest_date">
              <b-form-datepicker
                id="contest_date"
                name="contest_date"
                v-model="contest.contest_date"
                placeholder="Selecione uma data"
              ></b-form-datepicker>
            </b-form-group>
          </b-col>
        </b-row>

        <b-row>
          <b-col md="6">
            <b-form-group label="Valor do número R$" label-for="price">
              <b-form-input
                id="price"
                name="price"
                type="number"
                v-model="$v.contest.price.$model"
                :state="validateState('price')"
              ></b-form-input>

              <b-form-invalid-feedback v-if="!$v.contest.price.required"
                >Campo obrigatório</b-form-invalid-feedback
              >
              <b-form-invalid-feedback v-if="!$v.contest.price.minValue"
                >Valor mínimo deve ser R$ 0,01</b-form-invalid-feedback
              >
            </b-form-group>
          </b-col>
          <b-col md="6">
            <b-form-group label="Quantidade de números" label-for="quantity">
              <b-form-input
                id="quantity"
                name="quantity"
                type="number"
                v-model="$v.contest.quantity.$model"
                :state="validateState('quantity')"
              ></b-form-input>

              <b-form-invalid-feedback v-if="!$v.contest.quantity.required"
                >Campo obrigatório</b-form-invalid-feedback
              >
              <b-form-invalid-feedback v-if="!$v.contest.quantity.minValue"
                >Deve ter pelo menos 1 número</b-form-invalid-feedback
              >
            </b-form-group>
          </b-col>
        </b-row>

        <contest-sales-form
          :contest="contest"
          :addSale="handleAddSale"
          :removeSale="handleRemoveSale"
        />

        <b-form-group label="Descrição breve" label-for="short_description">
          <b-form-input
            id="short_description"
            name="short_description"
            v-model="$v.contest.short_description.$model"
            :state="validateState('short_description')"
          ></b-form-input>

          <b-form-invalid-feedback v-if="!$v.contest.short_description.required"
            >Campo obrigatório</b-form-invalid-feedback
          >
        </b-form-group>

        <b-form-group label="Descrição completa" label-for="full_description">
          <wysiwyg
            id="full_description"
            v-model="$v.contest.full_description.$model"
          />

          <b-form-invalid-feedback v-if="!$v.contest.full_description.required"
            >Campo obrigatório</b-form-invalid-feedback
          >
        </b-form-group>

        <b-form-group
          label="Contas bancárias"
          description="Selecione as contas para receber o pagamento dos números"
          label-for="full_description"
        >
          <b-form-select
            class="d-block w-100"
            v-model="contest.bank_accounts"
            :options="
              bank_accounts.map((acc) => ({ value: acc.id, text: acc.name }))
            "
            multiple
            :select-size="4"
          ></b-form-select>
        </b-form-group>

        <b-row>
          <b-col md="6">
            <b-form-group
              label="WhatsApp para contato"
              label-for="whatsapp_number"
            >
              <b-form-input
                id="whatsapp_number"
                name="whatsapp_number"
                v-model="$v.contest.whatsapp_number.$model"
                :state="validateState('whatsapp_number')"
              ></b-form-input>

              <b-form-invalid-feedback
                v-if="!$v.contest.whatsapp_number.required"
                >Campo obrigatório</b-form-invalid-feedback
              >
              <b-form-invalid-feedback
                v-if="!$v.contest.whatsapp_number.numeric"
                >Digite apenas números</b-form-invalid-feedback
              >
              <b-form-invalid-feedback
                v-if="!$v.contest.whatsapp_number.minLength"
                >Informe um número válido</b-form-invalid-feedback
              >
            </b-form-group>
          </b-col>
          <b-col md="6">
            <b-form-group label="Grupo do sorteio" label-for="whatsapp_group">
              <b-form-input
                id="whatsapp_group"
                name="whatsapp_group"
                v-model="$v.contest.whatsapp_group.$model"
                :state="validateState('whatsapp_group')"
              ></b-form-input>

              <b-form-invalid-feedback
                v-if="!$v.contest.whatsapp_group.required"
                >Campo obrigatório</b-form-invalid-feedback
              >
              <b-form-invalid-feedback v-if="!$v.contest.whatsapp_group.url"
                >Informe uma URL válida</b-form-invalid-feedback
              >
            </b-form-group>
          </b-col>
        </b-row>

        <contest-gallery-form
          :contest="contest"
          :addImage="handleAddImage"
          :removeImage="handleRemoveImage"
        />

        <div>
          <b-button
            type="submit"
            block
            variant="primary"
            class="mt-4"
            :disabled="loading"
            >Cadastrar sorteio</b-button
          >
        </div>
      </b-form>
    </div>
  </b-container>
</template>

<script>
import { validationMixin } from "vuelidate";
import {
  required,
  numeric,
  minValue,
  url,
  minLength,
} from "vuelidate/lib/validators";

import SalesFormVue from "../../components/Contests/Admin/SalesForm.vue";
import GalleryFormVue from "../../components/Contests/Admin/GalleryForm.vue";

import { createContest } from "../../services/contests";

export default {
  name: "AdminContestCreate",
  mixins: [validationMixin],
  components: {
    "contest-sales-form": SalesFormVue,
    "contest-gallery-form": GalleryFormVue,
  },
  data() {
    return {
      loading: false,
      bank_accounts: [
        {
          id: 1,
          type: "PIX",
          name: "Pagar com PIX",
          cc: null,
          agencia: null,
          dv: null,
          chave: "123.456.789-09",
        },
        {
          id: 2,
          type: "BANK",
          name: "Banco do Brasil",
          cc: "195866",
          agencia: "5901",
          dv: "6",
          chave: null,
        },
      ],
      contest: {
        title: "Título do sorteio",
        start_date: "2022-05-26",
        price: 5,
        sales: [
          {
            quantity: 2,
            price: 7,
          },
        ],
        quantity: 1,
        short_description: "Descrição curta",
        full_description: "Decrição completa",
        bank_accounts: [],
        contest_date: "2022-06-15",
        whatsapp_number: "11964513763",
        whatsapp_group: "https://chat.whatsapp.com/HDDZGY0grOwD6Y1Y5xRnXd",
        gallery: [],
      },
    };
  },
  validations: {
    contest: {
      title: { required },
      start_date: { required },
      price: { required, minValue: minValue(0.01) },
      quantity: { required, minValue: minValue(1) },
      short_description: { required },
      full_description: { required },
      whatsapp_number: { required, numeric, minLength: minLength(10) },
      whatsapp_group: { required, url },
    },
  },
  methods: {
    async onSubmit() {
      try {
        const { bank_accounts, gallery } = this.contest;

        this.$v.contest.$touch();

        if (this.$v.contest.$anyError) {
          this.$toasted.show(
            "Por favor, verifique se todos os campos estão corretos.",
            {
              type: "error",
              theme: "toasted-primary",
              position: "top-right",
              duration: 3000,
            }
          );
          return;
        }

        if (this.validateArray(bank_accounts)) {
          this.$toasted.show("Selecione uma conta para receber o pagamento", {
            type: "error",
            theme: "toasted-primary",
            position: "top-right",
            duration: 3000,
          });
          return;
        }

        if (this.validateArray(gallery)) {
          this.$toasted.show("Selecione ao menos uma imagem para o sorteio", {
            type: "error",
            theme: "toasted-primary",
            position: "top-right",
            duration: 3000,
          });
          return;
        }

        await createContest(this.contest);

        this.$toasted.show("Sorteio criado com sucesso!", {
          type: "success",
          theme: "toasted-primary",
          position: "top-right",
          duration: 3000,
        });

        this.$router.push({ name: "adminContestList" });
      } catch (error) {
        this.$toasted.show(error.message, {
          type: "error",
          theme: "toasted-primary",
          position: "top-right",
          duration: 3000,
        });
      }
    },
    handleAddSale() {
      const { sales } = this.contest;

      sales.push({
        quantity: 1,
        price: 0,
      });
    },
    handleRemoveSale(index) {
      const { sales } = this.contest;
      sales.splice(index, 1);
    },
    handleAddImage(image) {
      const { gallery } = this.contest;

      gallery.push(image);
    },
    handleRemoveImage(index) {
      const { gallery } = this.contest;
      gallery.splice(index, 1);
    },
    validateState(field) {
      const { $dirty, $error } = this.$v.contest[field];

      return $dirty ? !$error : null;
    },
    validateArray(array = []) {
      return array.length <= 0;
    },
  },
};
</script>

<style>
</style>