<template>
  <b-container fluid>
    <div class="p-4">
      <h2>Novo sorteio</h2>
      <b-form @submit.stop.prevent="handleCreateContest">
        <div class="d-flex justify-content-end my-4">
          <b-button type="submit" variant="primary">Criar sorteio</b-button>
        </div>

        <b-card no-body>
          <b-tabs card justified>
            <b-tab title="Detalhes" active>
              <contest-detail-form
                :v="$v"
                :contest="contest"
                :validateState="validateState"
              />
            </b-tab>

            <b-tab title="Números">
              <contest-numbers-form
                :v="$v"
                :contest="contest"
                :validateState="validateState"
              />
            </b-tab>

            <b-tab title="Premiações">
              <contest-reward-form
                :v="$v"
                :contest="contest"
                :validateState="validateState"
                :addReward="handleAddReward"
                :removeReward="handleRemoveReward"
            /></b-tab>

            <b-tab title="Contatos">
              <contest-contact-form
                :v="$v"
                :contest="contest"
                :validateState="validateState"
                :addContact="handleAddContact"
                :removeContact="handleRemoveContact"
              />
            </b-tab>

            <b-tab title="Imagens">
              <contest-gallery-form
                :contest="contest"
                :addImage="handleAddImage"
              />
            </b-tab>
          </b-tabs>
        </b-card>
      </b-form>
    </div>
  </b-container>
</template>

<script>
import { validationMixin } from "vuelidate";
import { required } from "vuelidate/lib/validators";

import DetailFormVue from "../../components/Contests/Admin/DetailForm.vue";
import NumbersFormVue from "../../components/Contests/Admin/NumbersForm.vue";
import RewardFormVue from "../../components/Contests/Admin/RewardForm.vue";
import ContactsFormVue from "../../components/Contests/Admin/ContactsForm.vue";
import GalleryFormVue from "../../components/Contests/Admin/GalleryForm.vue";
import { createContest } from "../../services/contests";

export default {
  name: "AdminContestCreate",
  mixins: [validationMixin],
  components: {
    "contest-detail-form": DetailFormVue,
    "contest-numbers-form": NumbersFormVue,
    "contest-reward-form": RewardFormVue,
    "contest-contact-form": ContactsFormVue,
    "contest-gallery-form": GalleryFormVue,
  },
  data() {
    return {
      contest: {
        title: "",
        short_description: "",
        full_description: "",
        start_date: "",
        days_to_end: 30,
        contest_date: "",
        number_start: "",
        number_price: "",
        number_quantity: "",
        number_per_customer: 1,
        rewards: [
          {
            number: 1,
            description: "",
          },
        ],
        contacts: [
          {
            name: "",
            value: "",
          },
        ],
        gallery: [],
      },
    };
  },
  validations: {
    contest: {
      title: { required },
      short_description: { required },
      full_description: { required },
      start_date: { required },
      contest_date: { required },
      number_start: { required },
      number_price: { required },
      number_quantity: { required },
    },
  },
  methods: {
    async handleCreateContest() {
      try {
        const { rewards, contacts, gallery } = this.contest;

        this.$v.contest.$touch();

        if (this.$v.contest.$anyError) {
          this.$toasted.show("Por favor, preencha todos os campos", {
            type: "error",
            theme: "toasted-primary",
            position: "top-right",
            duration: 3000,
          });
          return;
        }

        if (this.validateArray(rewards)) {
          this.$toasted.show("Informe ao menos uma premiação", {
            type: "error",
            theme: "toasted-primary",
            position: "top-right",
            duration: 3000,
          });
          return;
        }

        if (this.validateArray(contacts)) {
          this.$toasted.show("Informe ao menos um contato", {
            type: "error",
            theme: "toasted-primary",
            position: "top-right",
            duration: 3000,
          });
          return;
        }

        if (gallery.length <= 0) {
          this.$toasted.show("Selecione uma imagem para o sorteio", {
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
    handleAddReward() {
      const { rewards } = this.contest;

      rewards.push({
        rank: rewards.length + 1,
        description: "",
      });
    },
    handleRemoveReward(index) {
      const { rewards } = this.contest;
      rewards.splice(index, 1);
    },
    handleAddContact() {
      const { contacts } = this.contest;

      contacts.push({
        id: contacts.length + 1,
        name: "",
        link: "",
      });
    },
    handleRemoveContact(index) {
      const { contacts } = this.contest;
      contacts.splice(index, 1);
    },
    handleAddImage(image) {
      const { gallery } = this.contest;

      gallery.push(image);
    },
    validateState(field) {
      const { $dirty, $error } = this.$v.contest[field];

      return $dirty ? !$error : null;
    },
    validateArray(array = []) {
      let valid;
      array.forEach((item) => {
        Object.entries(item).forEach((values) => {
          if (values[1] === "") valid = true;
        });
      });

      return valid;
    },
  },
};
</script>

<style>
</style>