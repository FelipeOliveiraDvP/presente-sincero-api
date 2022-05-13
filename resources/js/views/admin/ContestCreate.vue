<template>
  <b-container fluid>
    <div class="p-4">
      <h2>Novo sorteio</h2>
      <b-form @submit="handleCreateContest">
        <h3>Detalhes</h3>

        <b-form-group id="title" label="Título" label-for="title">
          <b-form-input
            id="title"
            v-model="contest.title"
            type="text"
            placeholder="Título do sorteio"
            required
          ></b-form-input>
        </b-form-group>

        <b-form-group
          id="short_description"
          label="Descrição curta"
          label-for="short_description"
        >
          <b-form-input
            id="short_description"
            v-model="contest.short_description"
            type="text"
            placeholder="Descreva o sorteio em poucas palavras"
            required
          ></b-form-input>
        </b-form-group>

        <b-form-group
          id="full_description"
          label="Descrição completa"
          label-for="full_description"
        >
          <b-form-textarea
            id="full_description"
            v-model="contest.full_description"
            placeholder="Descreva em detalhes as premiações e como funciona o sorteio"
            rows="3"
            max-rows="6"
          ></b-form-textarea>
        </b-form-group>

        <b-row>
          <b-col md="4">
            <b-form-group
              id="start_date"
              label="Data de início"
              label-for="start_date"
            >
              <b-form-datepicker
                id="start_date"
                v-model="contest.start_date"
                class="mb-2"
                required
                placeholder="Escolha uma data"
              ></b-form-datepicker>
            </b-form-group>
          </b-col>
          <b-col md="4">
            <b-form-group
              id="days_to_end"
              label="Dias até o final do sorteio"
              label-for="days_to_end"
            >
              <b-form-spinbutton
                id="days_to_end"
                v-model="contest.days_to_end"
                locale="pt-BR"
                min="0"
                max="90"
                step="1"
              ></b-form-spinbutton>
            </b-form-group>
          </b-col>
          <b-col md="4">
            <b-form-group
              id="contest_date"
              label="Data do sorteio"
              label-for="contest_date"
            >
              <b-form-datepicker
                id="contest_date"
                v-model="contest.contest_date"
                class="mb-2"
                required
                placeholder="Escolha uma data"
              ></b-form-datepicker>
            </b-form-group>
          </b-col>
        </b-row>

        <h3>Números</h3>

        <b-row>
          <b-col md="3">
            <b-form-group
              id="number_price"
              label="Valor do número R$"
              label-for="number_price"
            >
              <b-form-input
                id="number_price"
                v-model="contest.numbers.price"
                type="number"
                required
              ></b-form-input>
            </b-form-group>
          </b-col>
          <b-col md="3">
            <b-form-group
              id="number_start"
              label="Número inicial do sorteio"
              label-for="number_start"
            >
              <b-form-input
                id="number_start"
                v-model="contest.numbers.start"
                type="text"
                required
              ></b-form-input>
            </b-form-group>
          </b-col>
          <b-col md="3">
            <b-form-group
              id="number_quantity"
              label="Quantidade de números disponíveis"
              label-for="number_quantity"
            >
              <b-form-spinbutton
                id="number_quantity"
                v-model="contest.numbers.quantity"
                locale="pt-BR"
                min="1"
                step="1"
              ></b-form-spinbutton>
            </b-form-group>
          </b-col>
          <b-col md="3">
            <b-form-group
              id="number_per_customer"
              label="Quantidade de números por cliente"
              label-for="number_per_customer"
              description="O cliente será notificado do limite durante a escolha dos números"
            >
              <b-form-spinbutton
                id="number_per_customer"
                v-model="contest.numbers.per_customer"
                locale="pt-BR"
                min="1"
                step="1"
              ></b-form-spinbutton>
            </b-form-group>
          </b-col>
        </b-row>

        <h4>Promoção</h4>

        <b-row>
          <b-col md="6">
            <b-form-group
              id="number_sale_price"
              label="Valor promocional R$"
              label-for="number_sale_price"
            >
              <b-form-input
                id="number_sale_price"
                v-model="contest.numbers.sale.price"
                type="number"
                required
              ></b-form-input>
            </b-form-group>
          </b-col>
          <b-col md="6">
            <b-form-group
              id="number_sale_quantity"
              label="Quantidade de números disponíveis"
              label-for="number_sale_quantity"
            >
              <b-form-spinbutton
                id="number_sale_quantity"
                v-model="contest.numbers.sale.quantity"
                locale="pt-BR"
                min="1"
                step="1"
              ></b-form-spinbutton>
            </b-form-group>
          </b-col>
        </b-row>

        <h3>Premiações</h3>

        <b-row
          v-for="(reward, index) in contest.rewards"
          :key="`reward-${index}`"
        >
          <b-col md="2">
            <b-form-group
              id="reward_rank"
              label="Ranking"
              label-for="reward_rank"
            >
              <b-form-input
                id="reward_rank"
                v-model="reward.rank"
                type="number"
                required
              ></b-form-input>
            </b-form-group>
          </b-col>
          <b-col md="8">
            <b-form-group
              id="reward_description"
              label="Descrição"
              label-for="reward_description"
            >
              <b-form-input
                id="reward_description"
                v-model="reward.description"
                type="text"
                required
              ></b-form-input>
            </b-form-group>
          </b-col>
          <b-col v-if="index > 0" md="2" class="d-flex align-items-end">
            <b-button variant="danger" @click="handleRemoveReward(index)">
              Remove
            </b-button>
          </b-col>
        </b-row>

        <b-button variant="primary" class="my-4" @click="handleAddReward"
          >Nova premiação</b-button
        >

        <h3>Contatos</h3>

        <b-row
          v-for="(contact, index) in contest.contacts"
          :key="`contact-${index}`"
        >
          <b-col md="4">
            <b-form-group
              id="contact_name"
              label="Nome"
              label-for="contact_name"
            >
              <b-form-input
                id="contact_name"
                v-model="contact.name"
                type="text"
                required
              ></b-form-input>
            </b-form-group>
          </b-col>
          <b-col md="4">
            <b-form-group
              id="contact_link"
              label="Link para o contato"
              label-for="contact_link"
            >
              <b-form-input
                id="contact_link"
                v-model="contact.link"
                type="text"
                required
              ></b-form-input>
            </b-form-group>
          </b-col>
          <b-col v-if="index > 0" md="2" class="d-flex align-items-end">
            <b-button variant="danger" @click="handleRemoveContact(index)">
              Remove
            </b-button>
          </b-col>
        </b-row>

        <b-button variant="primary" class="my-4" @click="handleAddContact"
          >Novo contato</b-button
        >

        <h3>Imagens</h3>

        <b-row>
          <b-col
            v-for="(image, index) in contest.gallery"
            :key="`image-${index}`"
            md="6"
            lg="4"
            xl="3"
          >
            <img :src="image.path" class="img-fluid" />
          </b-col>
        </b-row>

        <b-button type="submit" class="my-4 d-block w-100"
          >Criar sorteio</b-button
        >
      </b-form>
    </div>
  </b-container>
</template>

<script>
export default {
  name: "AdminContestCreate",
  data() {
    return {
      contest: {
        id: 1,
        title: "",
        short_description: "",
        full_description: "",
        start_date: "",
        days_to_end: 30,
        contest_date: "",
        numbers: {
          price: 5.0,
          start: "00000",
          quantity: 1,
          per_customer: 1,
          sale: {
            price: 0,
            quantity: 0,
          },
        },
        rewards: [
          {
            rank: 1,
            description: "",
          },
        ],
        contacts: [
          {
            name: "",
            link: "",
          },
        ],
        gallery: [
          {
            id: 1,
            main: true,
            path: "http://lorempixel.com.br/600/600",
          },
          {
            id: 2,
            path: "http://lorempixel.com.br/600/600",
          },
          {
            id: 3,
            link: "http://lorempixel.com.br/600/600",
          },
        ],
      },
    };
  },
  methods: {
    handleCreateContest(values) {
      console.log(values);
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
  },
};
</script>

<style>
</style>