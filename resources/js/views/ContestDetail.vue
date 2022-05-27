<template>
  <b-container class="page">
    <h1>
      Lorem ipsum dolor sit amet consectetur adipisicing elit. Tenetur
      distinctio
    </h1>
    <p>
      Lorem ipsum dolor sit, amet consectetur adipisicing elit. Amet debitis at
      corrupti, tenetur nobis molestiae soluta qui dolorum nostrum, minima sequi
      velit facere culpa ex minus in accusantium modi ratione?
    </p>

    <b-row>
      <b-col md="6">
        <img
          src="https://picsum.photos/600/600?random=1"
          alt="Sorteio"
          class="img-fluid"
        />
      </b-col>
      <b-col md="6" class="d-flex flex-column justify-content-between">
        <div>
          <p>
            Lorem ipsum, dolor sit amet consectetur adipisicing elit. Temporibus
            tempora nihil fugit, non omnis vel, ipsum odio sint eos voluptatum
            incidunt iste quis natus deserunt rem soluta consectetur minima
            quam.
          </p>
          <p>
            Lorem ipsum dolor sit, amet consectetur adipisicing elit. Suscipit,
            necessitatibus nisi molestias tenetur officia doloremque maxime.
            Odit delectus necessitatibus qui aspernatur neque nulla hic unde
            similique, doloribus nam tempora adipisci?
          </p>
        </div>

        <div>
          <p>
            Participe do grupo do sorteio no WhatsApp e mantenha-se informado
          </p>
          <b-button variant="success">
            Entrar no grupo <i class="fab fa-whatsapp"></i>
          </b-button>
          <div class="border border-success p-4 text-center text-success mt-4">
            COMPRE AGORA POR {{ formatPrice(price) }}
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
        <b-col md="6" lg="4" offset-lg="2">
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
    >
      <b-row>
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

    <div class="border p-4 mb-4 rounded">
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
                <del>{{ formatPrice(selectedNumbers.length * price) }}</del>
                por <strong>{{ formatPrice(currentSale.price) }}</strong>
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

    <b-modal
      id="customer-numbers"
      title="Meus números"
      ok-title="Ver meus números"
    >
      <p>
        Informe seu WhatsApp abaixo para conferir seus números nesse sorteio
      </p>
      <b-form-input placeholder="Informe seu WhatsApp" />
    </b-modal>
  </b-container>
</template>

<script>
import { mapActions } from "vuex";

import ContestNumberVue from "../components/Contests/ContestNumber.vue";
import moneyFormat from "../utils/moneyFormat";

export default {
  name: "ContestDetail",
  components: {
    "contest-number": ContestNumberVue,
  },
  data() {
    return {
      filter: "ALL",
      price: 5,
      numbers: [],
      selectedNumbers: [],
      filteredNumbers: [],
      magicNumbers: 0,
      // TODO: Retornar as promoções ordenada pela quantity DESC
      sales: [
        {
          quantity: 50,
          price: 2,
        },
        {
          quantity: 15,
          price: 2.5,
        },
        {
          quantity: 10,
          price: 3.5,
        },
      ],
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
    const { checkout } = this.$router.history.current.params;

    this.numbers = this.getNumbers();
    this.filteredNumbers = this.numbers;

    if (checkout) {
      this.selectedNumbers = [...checkout.numbers];
      this.calcSaleDiscount();
    }
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
  },
  methods: {
    ...mapActions({
      addToCart: "cart/updateCart",
    }),
    getNumbers() {
      const STATUS = ["FREE", "RESERVED", "PAID"];
      const n = [];

      for (let i = 0; i < 300; i++) {
        n.push({
          number: i,
          status: STATUS[Math.floor(Math.random() * 3)],
          customer: null,
        });
      }

      return n;
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
      this.$router.push({
        name: "checkout",
        params: {
          numbers: this.selectedNumbers,
          total: this.cart.totals,
          details: {
            slug: "slug-do-sorteio",
            short_description: "Descrição curta do sorteio",
            price: this.price,
          },
        },
      });
    },
    handleFilterNumbers(filter) {
      this.filter = filter;

      if (filter === "ALL") {
        this.filteredNumbers = this.numbers;
      } else {
        const filtered = this.numbers.filter((n) => n.status === filter);

        this.filteredNumbers = filtered;
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
      const freeNumbers = this.numbers.filter((n) => n.status === "FREE");

      this.selectedNumbers = freeNumbers.slice(0, this.magicNumbers);

      this.calcSaleDiscount();
    },
    getFirstFreeNumber() {
      return this.numbers.find((n) => n.status === "FREE");
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
        // const sale = this.sales.find((s) => s.quantity === i + 1);
        if (sale !== undefined) {
          partial += sale.price;
          this.currentSale = { ...sale };
        } else {
          // partial += this.price;
          partial += this.price;
        }
      }

      this.cart.totals = partial;
    },
    isSelected(number) {
      return !!this.selectedNumbers.find((n) => n.number === number.number);
    },
  },
};
</script>

<style>
</style>