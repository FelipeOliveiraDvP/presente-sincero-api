<template>
  <b-container class="page">
    <h1>
      {{ loading ? "Carregando..." : contest && contest.title }}
    </h1>
    <p>
      {{ loading ? "Carregando..." : contest && contest.short_description }}
    </p>

    <b-row>
      <b-col md="6" style="min-height: 550px">
        <img
          v-if="loading"
          class="img-fluid w-100"
          src="/img/placeholder.jpg"
          alt="Carregando"
        />
        <img
          v-else
          :src="contest && contest.gallery[0].path"
          :alt="contest && contest.title"
          class="img-fluid w-100"
        />
      </b-col>
      <b-col md="6" class="d-flex flex-column justify-content-between">
        <div>
          {{ loading ? "Carregando..." : contest && contest.full_description }}
        </div>

        <div
          v-if="
            contest &&
            (contest.show_percentage || contest.use_custom_percentage)
          "
        >
          <p>Números vendidos {{ contestPercentage }}%</p>
          <div class="border">
            <div
              class="bg-danger"
              :style="{ width: `${contestPercentage}%`, height: '50px' }"
            ></div>
          </div>
        </div>

        <div v-if="loading">Carregando informações...</div>
        <div v-else>
          <p>
            Participe do grupo do sorteio no WhatsApp e mantenha-se informado
          </p>
          <a :href="contest && contest.whatsapp_group" target="_blank">
            <b-button variant="success">
              Entrar no grupo <i class="fab fa-whatsapp"></i>
            </b-button>
          </a>
          <div class="border border-success p-4 text-center text-success mt-4">
            COMPRE AGORA POR {{ contest && formatPrice(contest.price) }}
          </div>
        </div>
      </b-col>
    </b-row>

    <div class="mt-4">
      <b-row>
        <b-col md="6">
          <b-button-group class="w-100">
            <b-button @click="handleFilterNumbers('ALL')" variant="light"
              >Todos ({{ numbers.length }})</b-button
            >
            <b-button @click="handleFilterNumbers('FREE')" variant="success"
              >Disponível ({{ countNumbersByStatus("FREE") }})</b-button
            >
            <b-button @click="handleFilterNumbers('RESERVED')" variant="warning"
              >Reservados ({{ countNumbersByStatus("RESERVED") }})</b-button
            >
            <b-button @click="handleFilterNumbers('PAID')" variant="danger"
              >Pagos ({{ countNumbersByStatus("PAID") }})</b-button
            >
          </b-button-group>
        </b-col>
        <b-col md="6" lg="4">
          <b-button
            v-b-modal.customer-numbers
            variant="secondary"
            class="d-block w-100 mt-2 mt-md-0"
            >MEUS NÚMEROS</b-button
          >
        </b-col>
      </b-row>
    </div>

    <div
      class="my-4 border border-secondary p-4"
      style="max-height: 400px; overflow-y: auto"
      @scroll="handleScroll"
    >
      <my-loader v-if="loading" />
      <b-row v-else>
        <b-col
          :key="number.number"
          v-for="number in filteredNumbers"
          cols="4"
          md="2"
          lg="1"
          class="p-2 text-center"
        >
          <contest-number
            :number="number"
            :isSelected="isSelected(number)"
            @select="handleSelectNumber"
          />
        </b-col>
      </b-row>
    </div>

    <div class="border p-4 my-4 rounded">
      <b-row>
        <b-col md="6">
          <h4>Números selecionados</h4>
          <b-row style="max-height: 120px; overflow-y: auto">
            <b-col cols="12" v-if="selectedNumbers.length === 0">
              Você ainda não escolheu nenhum número
            </b-col>
            <b-col
              :key="selected.number"
              v-for="selected in selectedNumbers"
              cols="6"
              md="4"
              lg="3"
              class="p-2 text-center"
            >
              <contest-number
                :number="selected"
                allowRemove
                @remove="handleRemoveSelectedNumber"
              />
            </b-col>
          </b-row>
        </b-col>
        <b-col md="6">
          <h5>Números mágicos</h5>
          <p>
            Não sabe quais números escolher? Então informe abaixo a quantidade
            de números que deseja comprar que nosso sistema vai escolher
            automáticamente para você.
          </p>
          <b-row>
            <b-col lg="6">
              <b-form-spinbutton
                v-model="magicNumbers"
                :max="countNumbersByStatus('FREE')"
                @change="handleAddMagicNumber"
              />
            </b-col>
            <b-col lg="6" class="d-flex justify-content-between mt-4 mt-lg-0">
              <b-button variant="primary" @click="decrementMagicNumber(10)"
                >-10</b-button
              >
              <b-button variant="primary" @click="incrementMagicNumber(10)"
                >+10</b-button
              >
              <b-button variant="primary" @click="decrementMagicNumber(50)"
                >-50</b-button
              >
              <b-button variant="primary" @click="incrementMagicNumber(50)"
                >+50</b-button
              >
            </b-col>
          </b-row>
        </b-col>
        <b-col cols="12"> <div class="divider"></div> </b-col>
        <b-col md="6">
          <h5>Total</h5>
          <div
            class="
              d-flex
              flex-column
              justify-content-center
              align-items-start
              h-100
            "
          >
            <p>
              {{ selectedNumbers.length }} X
              {{ formattedCartTotal }}
            </p>
            <div v-if="hasSale">
              <h5>PROMOÇÃO</h5>
              <p>
                DE
                <del>{{
                  contest && formatPrice(selectedNumbers.length * contest.price)
                }}</del>
                por
                <strong>{{
                  formatPrice(selectedNumbers.length * currentSale.price)
                }}</strong>
              </p>
            </div>
          </div>
        </b-col>
        <b-col md="6">
          <div class="d-flex justify-content-end align-items-center h-100 mt-2">
            <b-button
              variant="primary"
              size="lg"
              @click="handleCheckout"
              :disabled="selectedNumbers.length === 0"
            >
              COMPRAR NÚMEROS
            </b-button>
          </div>
        </b-col>
      </b-row>
    </div>

    <contest-numbers-modal :contestId="contest && contest.id" />

    <simple-register-modal />
  </b-container>
</template>

<script>
import { mapActions } from "vuex";

import ContestNumberVue from "@/components/Contests/ContestNumber.vue";
import LoaderVue from "@/components/_commons/Loader.vue";
import ContestNumbersModal from "@/components/Customers/ContestNumbersModal.vue";
import SimpleRegisterModal from "@/components/Auth/SimpleRegisterModal.vue";

import { getContestBySlug } from "@/services/contests";

import moneyFormat from "@/utils/moneyFormat";

export default {
  name: "ContestDetail",
  components: {
    "contest-number": ContestNumberVue,
    "my-loader": LoaderVue,
    "contest-numbers-modal": ContestNumbersModal,
    "simple-register-modal": SimpleRegisterModal,
  },
  data() {
    return {
      loading: false,
      filter: "ALL",
      contest: null,
      numbers: [],
      selectedNumbers: [],
      filteredNumbers: [],
      quantity: 0,
      partial: 300,
      current: 0,
      magicNumbers: 0,
      sales: [],
      currentSale: {
        quantity: 0,
        price: 0,
      },
      cart: {
        totals: 0,
      },
    };
  },
  mounted() {
    this.getContestData();
  },
  computed: {
    formattedCartTotal() {
      return moneyFormat(this.cart.totals);
    },
    hasSale() {
      return this.sales.find(
        (sale) => this.selectedNumbers.length >= sale.quantity
      );
    },
    contestPercentage() {
      const {
        use_custom_percentage,
        show_percentage,
        paid_percentage,
        custom_percentage,
      } = this.contest;

      if (use_custom_percentage) return custom_percentage * 100;

      if (show_percentage) return paid_percentage * 100;

      return 0;
    },
  },
  methods: {
    ...mapActions({
      addToCart: "cart/updateCart",
    }),
    async getContestData() {
      try {
        const { slug } = this.$router.history.current.params;

        this.loading = true;

        const result = await getContestBySlug(slug);
        const numbersArray = result.numbers ? JSON.parse(result.numbers) : [];

        this.contest = { ...result };
        this.quantity = result.quantity;
        this.numbers = numbersArray.map((number) => JSON.parse(number));
        this.filteredNumbers = [...this.numbers].splice(0, this.partial);
        this.current += this.partial;
        this.sales = result.sales
          .map((sale) => ({
            price: sale.price,
            quantity: sale.quantity,
          }))
          .reverse();

        this.magicNumbers = this.selectedNumbers.length;
        this.calcSaleDiscount();
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
    handleSelectNumber(number) {
      const isSelected = !!this.selectedNumbers.find(
        (n) => n.number === number.number
      );

      if (!isSelected && number.status === "FREE") {
        this.selectedNumbers.push(number);

        this.calcSaleDiscount();
      }
    },
    handleRemoveSelectedNumber(number) {
      const index = this.selectedNumbers.indexOf(number);

      this.selectedNumbers.splice(index, 1);

      this.calcSaleDiscount();
    },
    formatPrice(price) {
      return moneyFormat(price);
    },
    handleCheckout() {
      const { authenticated } = this.$store.state.auth;

      if (!authenticated) {
        this.$bvModal.show("simple-register");
        return;
      }

      this.$router.push({
        name: "checkout",
        params: {
          numbers: this.selectedNumbers,
          total: this.cart.totals,
          details: {
            contestId: this.contest.id,
            title: this.contest.title,
            slug: this.contest.slug,
            short_description: this.contest.short_description,
            price: this.contest.price,
            sale: this.currentSale,
          },
        },
      });
    },
    handleFilterNumbers(filter) {
      this.filter = filter;
      this.current = 0;

      if (filter === "ALL") {
        this.filteredNumbers = [...this.numbers].splice(0, this.partial);
      } else {
        const filtered = this.numbers.filter((n) => n.status === filter);

        this.filteredNumbers = [...filtered].splice(0, this.partial);
      }
    },
    countNumbersByStatus(status) {
      const filtered = this.numbers.filter((n) => n.status === status);

      return filtered.length;
    },
    incrementMagicNumber(quantity) {
      const availableNumbers = this.countNumbersByStatus("FREE");

      if (this.magicNumbers + quantity > availableNumbers) {
        this.magicNumbers = availableNumbers;
      } else {
        this.magicNumbers += quantity;
      }

      this.handleAddMagicNumber();
    },
    decrementMagicNumber(quantity) {
      if (this.magicNumbers - quantity < 0) {
        this.magicNumbers = 0;
      } else {
        this.magicNumbers -= quantity;
      }

      this.handleAddMagicNumber();
    },
    handleAddMagicNumber() {
      const freeNumbers = this.filteredNumbers.filter(
        (n) => n.status === "FREE"
      );

      this.selectedNumbers = freeNumbers.slice(0, this.magicNumbers);

      this.calcSaleDiscount();
    },
    getFirstFreeNumber() {
      return this.filteredNumbers.find((n) => n.status === "FREE");
    },
    calcSaleDiscount() {
      const { length } = this.selectedNumbers;

      if (length === 0) {
        this.cart.totals = 0;
        return;
      }

      let partial = 0;

      const sale = this.sales.find((s) => length >= s.quantity);

      for (let i = 0; i < length; i++) {
        if (sale !== undefined) {
          partial += sale.price;
          this.currentSale = { ...sale };
        } else {
          partial += this.contest.price;
        }
      }

      this.cart.totals = partial;
    },
    isSelected(number) {
      return !!this.selectedNumbers.find((n) => n.number === number.number);
    },
    handleScroll(el) {
      console.log(this.current < this.quantity - this.partial);
      if (
        el.srcElement.offsetHeight + el.srcElement.scrollTop >=
          el.srcElement.scrollHeight &&
        this.current < this.quantity - this.partial
      ) {
        const filtered =
          this.filter === "ALL"
            ? [...this.numbers]
            : [...this.numbers].filter((n) => n.status === this.filter);

        this.current += this.partial;

        this.filteredNumbers = [...filtered].splice(this.current, this.partial);
      }
    },
  },
};
</script>

<style>
</style>