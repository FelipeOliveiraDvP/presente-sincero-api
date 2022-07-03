<template>
  <b-container fluid>
    <div class="p-4">
      <h2>Editar sorteio</h2>

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
                :min="minStartDate"
                :disabled="true"
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
          <b-col md="4">
            <b-form-group label="Valor do número R$" label-for="price">
              <the-mask
                :mask="['#,##', '##,##', '###,##', '#.###,##']"
                class="form-control"
                id="price"
                name="price"
                v-model="$v.contest.price.$model"
                :state="validateState('price')"
              ></the-mask>

              <b-form-invalid-feedback v-if="!$v.contest.price.required"
                >Campo obrigatório</b-form-invalid-feedback
              >
              <b-form-invalid-feedback v-if="!$v.contest.price.minValue"
                >Valor mínimo deve ser R$ 0,50</b-form-invalid-feedback
              >
            </b-form-group>
          </b-col>
          <b-col md="4">
            <b-form-group label="Quantidade de números" label-for="quantity">
              <b-form-input
                id="quantity"
                name="quantity"
                type="number"
                v-model="$v.contest.quantity.$model"
                :state="validateState('quantity')"
                :disabled="true"
              ></b-form-input>

              <b-form-invalid-feedback v-if="!$v.contest.quantity.required"
                >Campo obrigatório</b-form-invalid-feedback
              >
              <b-form-invalid-feedback v-if="!$v.contest.quantity.minValue"
                >Deve ter pelo menos 1 número</b-form-invalid-feedback
              >
            </b-form-group>
          </b-col>
          <b-col md="4">
            <b-form-group
              label="Dias para pagamento"
              description="Quantidade de dias após a reserva em que o pagamento ficará disponível"
              label-for="max_reserve_days"
            >
              <b-form-input
                id="max_reserve_days"
                name="max_reserve_days"
                type="number"
                v-model="$v.contest.max_reserve_days.$model"
                :state="validateState('max_reserve_days')"
              ></b-form-input>

              <b-form-invalid-feedback
                v-if="!$v.contest.max_reserve_days.required"
                >Campo obrigatório</b-form-invalid-feedback
              >
              <b-form-invalid-feedback
                v-if="!$v.contest.max_reserve_days.minValue"
                >Deve ter pelo menos 1 dia</b-form-invalid-feedback
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

        <h3>Contas bancárias</h3>
        <b-form-group
          label="Contas disponíveis"
          description="Selecione as contas para receber o pagamento dos números"
          label-for="full_description"
        >
          <b-form-checkbox-group
            v-if="bank_accounts.length > 0"
            class="d-block w-100"
            v-model="contest.bank_accounts"
            :options="
              bank_accounts.map((acc) => ({
                value: acc.id,
                text: acc.name,
              }))
            "
          ></b-form-checkbox-group>
          <div v-else>
            <b-alert show variant="warning">
              Não existem contas cadastradas.
              <hr />
              <router-link class="alert-link" to="/admin/contas">
                Cadastrar contas
              </router-link>
            </b-alert>
          </div>
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
            >Salvar alterações no sorteio</b-button
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
import moment from "moment";
import isEmpty from "lodash.isempty";

import SalesFormVue from "@/components/Contests/Admin/SalesForm.vue";
import GalleryFormVue from "@/components/Contests/Admin/GalleryForm.vue";

import { editContest, getContest } from "@/services/contests";
import { listBankAccounts } from "@/services/bankAccounts";

export default {
  name: "AdminContestDetail",
  mixins: [validationMixin],
  components: {
    "contest-sales-form": SalesFormVue,
    "contest-gallery-form": GalleryFormVue,
  },
  data() {
    return {
      loading: false,
      bank_accounts: [],
      contest: {
        id: null,
        title: "",
        start_date: moment().add(1, "day").format("YYYY-MM-DD"),
        contest_date: null,
        price: 0.5,
        quantity: 1,
        max_reserve_days: 1,
        sales: [],
        short_description: "",
        full_description: "",
        bank_accounts: [],
        whatsapp_number: "",
        whatsapp_group: "",
        gallery: [],
      },
    };
  },
  computed: {
    minStartDate() {
      return moment().add(1, "day").format("YYYY-MM-DD");
    },
  },
  mounted() {
    const { id } = this.$router.history.current.params;

    this.getContestData(id);
    this.getBankAccountsData();
  },
  validations: {
    contest: {
      title: { required },
      start_date: { required },
      price: { required, minValue: minValue(0.5) },
      quantity: { required, minValue: minValue(1) },
      max_reserve_days: { required, minValue: minValue(1) },
      short_description: { required },
      full_description: { required },
      whatsapp_number: { required, numeric, minLength: minLength(10) },
      whatsapp_group: { required, url },
    },
  },
  methods: {
    async onSubmit() {
      try {
        const { bank_accounts, gallery, price, sales } = this.contest;

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

        if (isEmpty(full_description)) {
          this.$toasted.show("Informe a descrição completa para o sorteio", {
            type: "error",
            theme: "toasted-primary",
            position: "top-right",
            duration: 3000,
          });
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

        this.contest.gallery = gallery.map((img) =>
          img && img.path !== null ? img?.path : ""
        );
        this.contest.sales = sales.map((s) => ({
          quantity: s.quantity,
          price: s.price / 100,
        }));
        this.contest.price = price / 100;

        const result = await editContest(this.contest.id, this.contest);

        this.$toasted.show(result.message, {
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
    async getContestData(id) {
      try {
        this.loading = true;
        const result = await getContest(id);

        this.contest = {
          ...result,
          bank_accounts: result.bank_accounts.map((acc) => acc.id),
        };
      } catch (error) {
        this.$toasted.show(error.message, {
          type: "info",
          theme: "toasted-primary",
          position: "top-right",
          duration: 3000,
        });

        this.$router.push({ name: "adminContestList" });
      } finally {
        this.loading = false;
      }
    },
    async getBankAccountsData() {
      const result = await listBankAccounts();

      this.bank_accounts = result.data;
    },
    isCheckedAccount(account) {
      return this.contest.bank_accounts.includes(account.id);
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