<template>
  <Container>
    <div v-if="loading || contest === null">
      <a-skeleton-input
        size="large"
        active
        block
        style="height: 40px; width: 100%"
      />
      <a-row :gutter="[16, 16]">
        <a-col :xs="24" :md="12" :lg="8">
          <a-skeleton-image />
        </a-col>
        <a-col :xs="24" :md="12" :lg="16">
          <a-skeleton-input
            size="large"
            active
            block
            style="height: 30px; width: 100%"
          />
          <a-skeleton-input
            size="large"
            active
            block
            style="height: 30px; width: 100%"
          />
          <a-skeleton-input
            size="large"
            active
            block
            style="height: 30px; width: 100%"
          />
          <a-skeleton-input
            size="large"
            active
            block
            style="height: 30px; width: 100%"
          />
          <a-skeleton-input
            size="large"
            active
            block
            style="height: 30px; width: 100%"
          />
          <a-skeleton-input
            size="large"
            active
            block
            style="height: 80px; width: 100%"
          />
        </a-col>
      </a-row>
      <a-skeleton-input
        size="large"
        active
        block
        style="height: 80px; width: 100%"
      />
    </div>

    <div v-else>
      <a-breadcrumb>
        <a-breadcrumb-item>
          <router-link to="/"> Home </router-link>
        </a-breadcrumb-item>
        <a-breadcrumb-item>
          <router-link :to="`/${contest && contest.seller.username}`">
            {{ contest && contest.seller.name }}
          </router-link>
        </a-breadcrumb-item>
      </a-breadcrumb>
      <a-typography-title>{{ contest && contest.title }}</a-typography-title>

      <a-row :gutter="[16, 16]">
        <a-col :xs="24" :md="12" :lg="8">
          <a-image
            :preview="{ visible: false }"
            :src="
              contest.gallery ? contest.gallery[0].path : '/img/placeholder.jpg'
            "
            placeholder
            @click="visible = true"
            width="100%"
            height="400px"
          />
          <div style="display: none">
            <a-image-preview-group
              :preview="{ visible, onVisibleChange: (vis) => (visible = vis) }"
            >
              <a-image
                v-for="image in contest.gallery"
                :key="image.id"
                :src="image.path"
              />
            </a-image-preview-group>
          </div>
        </a-col>
        <a-col :xs="24" :md="12" :lg="16">
          <p>{{ contest.full_description }}</p>

          <div v-if="true">
            <a-typography-title :level="4">Números vendidos</a-typography-title>
            <a-progress :stroke-width="40" :percent="contestPercentage" />
          </div>
        </a-col>
      </a-row>

      <!-- 
        :onSelect="handleSelectNumber"
        :onRemove="handleRemoveSelectedNumber"
        :onSelect="handleSelectNumber"
        :onRemove="handleRemoveSelectedNumber"
      -->
      <contest-numbers-list
        :numbers="filteredNumbers"
        :filter="filter"
        @onFilter="handleFilterNumbers"
      >
        <template #extra>
          <a-button type="primary" size="large" block>Meus números</a-button>
        </template>
      </contest-numbers-list>

      <contest-numbers-modal />
    </div>

    <!-- <b-container class="page">
    <h1>
      {{ loading ? "Carregando..." : contest && contest.title }}
    </h1>
    <p>
      {{ loading ? "Carregando..." : contest && contest.short_description }}
    </p>

    <b-row>
      <b-col md="6" style="min-height: 550px">
        <img v-if="loading" class="img-fluid w-100" src="/img/placeholder.jpg" alt="Carregando" />
        <b-carousel v-else-if="contest !== null" id="contest-gallery" :interval="4000" controls indicators
          background="#ababab" img-width="1024" img-height="480" style="text-shadow: 1px 1px 2px #333">
          <b-carousel-slide v-for="image in contest.gallery" :key="image.id" :img-src="image.path"></b-carousel-slide>
        </b-carousel>
      </b-col>
      <b-col md="6" class="d-flex flex-column justify-content-between">
        <div>
          {{ loading ? "Carregando..." : contest && contest.full_description }}
        </div>

        <div v-if="
          contest &&
          (contest.show_percentage || contest.use_custom_percentage)
        ">
          <p>Números vendidos {{ contestPercentage }}%</p>
          <div class="border">
            <div class="bg-danger" :style="{ width: `${contestPercentage}%`, height: '50px' }"></div>
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
            <b-button @click="handleFilterNumbers('ALL')" variant="light">Todos ({{ numbers.length }})</b-button>
            <b-button @click="handleFilterNumbers('FREE')" variant="success">Disponível ({{ countNumbersByStatus("FREE")
            }})</b-button>
            <b-button @click="handleFilterNumbers('RESERVED')" variant="warning">Reservados ({{
                countNumbersByStatus("RESERVED")
            }})</b-button>
            <b-button @click="handleFilterNumbers('PAID')" variant="danger">Pagos ({{ countNumbersByStatus("PAID") }})
            </b-button>
          </b-button-group>
        </b-col>
        <b-col md="6" lg="4">
          <b-button v-b-modal.customer-numbers variant="secondary" class="d-block w-100 mt-2 mt-md-0">MEUS NÚMEROS
          </b-button>
        </b-col>
      </b-row>
    </div>

    <div class="my-4 border border-secondary p-4" style="max-height: 400px; overflow-y: auto" @scroll="handleScroll">
      <my-loader v-if="loading" />
      <b-row v-else>
        <b-col :key="number.number" v-for="number in filteredNumbers" cols="4" md="2" lg="1" class="p-2 text-center">
          <contest-number :number="number" :isSelected="isSelected(number)" @select="handleSelectNumber" />
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
            <b-col :key="selected.number" v-for="selected in selectedNumbers" cols="6" md="4" lg="3"
              class="p-2 text-center">
              <contest-number :number="selected" allowRemove @remove="handleRemoveSelectedNumber" />
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
              <b-form-spinbutton v-model="magicNumbers" :max="countNumbersByStatus('FREE')"
                @change="handleAddMagicNumber" />
            </b-col>
            <b-col lg="6" class="d-flex justify-content-between mt-4 mt-lg-0">
              <b-button variant="primary" @click="decrementMagicNumber(10)">-10</b-button>
              <b-button variant="primary" @click="incrementMagicNumber(10)">+10</b-button>
              <b-button variant="primary" @click="decrementMagicNumber(50)">-50</b-button>
              <b-button variant="primary" @click="incrementMagicNumber(50)">+50</b-button>
            </b-col>
          </b-row>
        </b-col>
        <b-col cols="12">
          <div class="divider"></div>
        </b-col>
        <b-col md="6">
          <h5>Total</h5>
          <div class="
              d-flex
              flex-column
              justify-content-center
              align-items-start
              h-100
            ">
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
            <b-button variant="primary" size="lg" @click="handleCheckout" :disabled="selectedNumbers.length === 0">
              COMPRAR NÚMEROS
            </b-button>
          </div>
        </b-col>
      </b-row>
    </div>

    <contest-numbers-modal :contestId="contest && contest.id" />

    <simple-register-modal @onsuccess="handleCheckout" />
  </b-container> -->
  </Container>
</template>

<script>
import { computed, defineComponent, onMounted, reactive, ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import { notification } from "ant-design-vue";
import { useStore } from "vuex";

import Container from "@/components/_commons/Container.vue";
import ContestNumbersModal from "@/components/Customers/ContestNumbersModal.vue";
import ContestNumbersList from "@/components/Contests/ContestNumbersList.vue";
import SimpleRegisterModal from "@/components/Auth/SimpleRegisterModal.vue";

import { getContestBySlug } from "@/services/contests";

import { moneyFormat } from "@/utils/moneyFormat";
import { contestPercentage as percentage } from "@/utils/contestPercentage";

export default defineComponent({
  name: "ContestDetail",
  setup() {
    const visible = ref(false);
    const loading = ref(false);
    const showNumbersModal = ref(false);
    const filter = ref("ALL");
    const sales = ref([]);
    const numbers = ref([]);
    const selectedNumbers = ref([]);
    const filteredNumbers = ref([]);
    const quantity = ref(0);
    const magicNumbers = ref(0);
    const contest = ref({
      id: 0,
      user_id: 0,
      winner_id: null,
      title: "",
      slug: "",
      start_date: "",
      max_reserve_days: 1,
      show_percentage: false,
      use_custom_percentage: false,
      paid_percentage: 0,
      custom_percentage: 0,
      contest_date: null,
      price: 0,
      quantity: 0,
      short_description: "",
      full_description: "",
      whatsapp_number: "",
      whatsapp_group: "",
      numbers: "[]",
      status: "ACTIVE",
      created_at: "",
      updated_at: "",
      deleted_at: null,
      seller: {
        id: 0,
        name: "",
        username: "",
      },
      gallery: null,
      bank_accounts: [],
      sales: [],
    });
    const currentSale = reactive({
      quantity: 0,
      price: 0,
    });
    const cart = reactive({
      totals: 0,
    });

    const formattedCartTotal = computed(() => moneyFormat(cart.totals));
    const hasSale = computed(() =>
      sales.value.find((sale) => selectedNumbers.value.length >= sale.quantity)
    );
    const contestPercentage = computed(() => percentage(contest));

    const route = useRoute();
    const router = useRouter();
    const store = useStore();

    async function getContestData() {
      try {
        loading.value = true;

        const { username, slug } = route.params;

        const result = await getContestBySlug(username, slug);
        const numbersArray = result.numbers ? JSON.parse(result.numbers) : [];

        contest.value = { ...result };
        quantity.value = result.quantity;
        numbers.value = numbersArray.map((number) => JSON.parse(number));
        filteredNumbers.value = [...numbers.value];
        magicNumbers.value = selectedNumbers.value.length;
        sales.value = result.sales
          .map((sale) => ({
            price: sale.price,
            quantity: sale.quantity,
          }))
          .reverse();

        calcSaleDiscount();
      } catch (error) {
        notification.error({
          message: error.message,
        });
      } finally {
        loading.value = false;
      }
    }

    function calcSaleDiscount() {
      const { length } = selectedNumbers.value;

      if (length === 0) {
        cart.totals = 0;
        return;
      }

      let partial = 0;

      const sale = sales.value.find((s) => length >= s.quantity);

      for (let i = 0; i < length; i++) {
        if (sale !== undefined) {
          partial += sale.price;

          currentSale.quantity = sale.quantity;
          currentSale.price = sale.price;
        } else {
          partial += contest.value.price;
        }
      }

      cart.totals = partial;
    }

    function formatPrice(value) {
      return moneyFormat(value);
    }

    // List
    function countNumbersByStatus(status) {
      const filtered = numbers.value.filter((n) => n.status === status);

      return filtered.length;
    }

    function incrementMagicNumber(quantity) {
      const availableNumbers = countNumbersByStatus("FREE");

      if (magicNumbers.value + quantity > availableNumbers) {
        magicNumbers.value = availableNumbers;
      } else {
        magicNumbers.value += quantity;
      }

      addMagicNumbers();
    }

    function decrementMagicNumber(quantity) {
      if (magicNumbers.value - quantity < 0) {
        magicNumbers.value = 0;
      } else {
        magicNumbers.value -= quantity;
      }

      addMagicNumbers();
    }

    function addMagicNumbers() {
      const freeNumbers = filteredNumbers.value.filter(
        (n) => n.status === "FREE"
      );

      selectedNumbers.value = freeNumbers.slice(0, magicNumbers.value);

      calcSaleDiscount();
    }

    // List
    function isSelected(number) {
      return !!selectedNumbers.value.find((n) => n.number === number.number);
    }

    function handleSelectNumber(number) {
      const isSelected = !!selectedNumbers.value.find(
        (n) => n.number === number.number
      );

      if (!isSelected && number.status === "FREE") {
        selectedNumbers.value = [...selectedNumbers.value, number];
        calcSaleDiscount();
      }
    }

    function handleRemoveSelectedNumber(number) {
      const index = selectedNumbers.value.indexOf(number);
      const newArray = [...selectedNumbers.value].splice(index, 1);

      selectedNumbers.value = [...newArray];

      calcSaleDiscount();
    }

    function handleFilterNumbers(current) {
      filter.value = current;

      if (filter === "ALL") {
        filteredNumbers.value = [...numbers.value];
      } else {
        const filtered = numbers.value.filter((n) => n.status === current);

        filteredNumbers.value = [...filtered];
      }
    }

    function handleCheckout() {
      const { authenticated } = store.state.auth;

      if (!authenticated) {
        // TODO: Mostrar modal de login simples
        return;
      }

      router.push({
        name: "checkout",
        params: {
          numbers: selectedNumbers.value,
          total: cart.totals,
          details: {
            contestId: contest.value.id,
            title: contest.value.title,
            slug: contest.value.slug,
            seller: contest.value.seller,
            price: contest.value.price,
            short_description: contest.value.short_description,
            whatsapp_number: contest.value.whatsapp_number,
            whatsapp_group: contest.value.whatsapp_group,
            sale: currentSale,
          },
        },
      });
    }

    onMounted(async () => {
      await getContestData();
    });

    return {
      visible,
      loading,
      showNumbersModal,
      filter,
      contest,
      sales,
      numbers,
      selectedNumbers,
      filteredNumbers,
      quantity,
      magicNumbers,
      currentSale,
      cart,
      formattedCartTotal,
      hasSale,
      contestPercentage,
      formatPrice,
      incrementMagicNumber,
      decrementMagicNumber,
      isSelected,
      handleSelectNumber,
      handleRemoveSelectedNumber,
      handleCheckout,
      handleFilterNumbers,
    };
  },
  components: {
    Container,
    ContestNumbersList,
    ContestNumbersModal,
    SimpleRegisterModal,
  },
});
// export default defineComponent({
//   name: "ContestDetail",
//   components: {
//     ContestNumbersModal,
//     SimpleRegisterModal,
//     Container,
//     ContestNumbersList,
//   },
//   data() {
//     return {
//       visible: ref(false),
//       loading: false,
//       filter: "ALL",
//       contest: null,
//       numbers: [],
//       selectedNumbers: [],
//       filteredNumbers: [],
//       quantity: 0,
//       partial: 300,
//       current: 0,
//       magicNumbers: 0,
//       sales: [],
//       currentSale: {
//         quantity: 0,
//         price: 0,
//       },
//       cart: {
//         totals: 0,
//       },
//     };
//   },
//   mounted() {
//     this.getContestData();
//   },
//   computed: {
//     formattedCartTotal() {
//       return moneyFormat(this.cart.totals);
//     },
//     hasSale() {
//       return this.sales.find(
//         (sale) => this.selectedNumbers.length >= sale.quantity
//       );
//     },
//     contestPercentage() {
//       const {
//         use_custom_percentage,
//         show_percentage,
//         paid_percentage,
//         custom_percentage,
//       } = contest.value;
//       let total = 0;

//       if (use_custom_percentage) total = custom_percentage * 100;

//       if (show_percentage) total = paid_percentage * 100;

//       return total.toFixed(2);
//     },
//   },
//   methods: {
//     ...mapActions({
//       addToCart: "cart/updateCart",
//     }),
//     async getContestData() {
//       try {
//         const { username, slug } = this.$route.params;

//         this.loading = true;

//         const result = await getContestBySlug(username, slug);
//         const numbersArray = result.numbers ? JSON.parse(result.numbers) : [];

//         this.contest = { ...result };
//         this.quantity = result.quantity;
//         this.numbers = numbersArray.map((number) => JSON.parse(number));
//         this.filteredNumbers = [...this.numbers].splice(0, this.partial);
//         this.current += this.partial;
//         this.sales = result.sales
//           ? result.sales
//               .map((sale) => ({
//                 price: sale.price,
//                 quantity: sale.quantity,
//               }))
//               .reverse()
//           : [];

//         this.magicNumbers = this.selectedNumbers.length;
//         this.calcSaleDiscount();
//       } catch (error) {
//         console.error(error);
//       } finally {
//         this.loading = false;
//       }
//     },
//     handleSelectNumber(number) {
//       const isSelected = !!this.selectedNumbers.find(
//         (n) => n.number === number.number
//       );

//       if (!isSelected && number.status === "FREE") {
//         this.selectedNumbers.push(number);

//         this.calcSaleDiscount();
//       }
//     },
//     handleRemoveSelectedNumber(number) {
//       const index = this.selectedNumbers.indexOf(number);

//       this.selectedNumbers.splice(index, 1);

//       this.calcSaleDiscount();
//     },
//     formatPrice(price) {
//       return moneyFormat(price);
//     },
//     handleCheckout() {
//       const { authenticated } = this.$store.state.auth;

//       if (!authenticated) {
//         // this.$bvModal.show("simple-register");
//         return;
//       }

//       this.$router.push({
//         name: "checkout",
//         params: {
//           numbers: this.selectedNumbers,
//           total: this.cart.totals,
//           details: {
//             contestId: this.contest.id,
//             title: this.contest.title,
//             slug: this.contest.slug,
//             short_description: this.contest.short_description,
//             whatsapp_group: this.contest.whatsapp_group,
//             price: this.contest.price,
//             sale: this.currentSale,
//           },
//         },
//       });
//     },
//     handleFilterNumbers(filter) {
//       this.filter = filter;
//       this.current = 0;

//       if (filter === "ALL") {
//         this.filteredNumbers = [...this.numbers].splice(0, this.partial);
//       } else {
//         const filtered = this.numbers.filter((n) => n.status === filter);

//         this.filteredNumbers = [...filtered].splice(0, this.partial);
//       }
//     },
//     countNumbersByStatus(status) {
//       const filtered = this.numbers.filter((n) => n.status === status);

//       return filtered.length;
//     },
//     incrementMagicNumber(quantity) {
//       const availableNumbers = this.countNumbersByStatus("FREE");

//       if (this.magicNumbers + quantity > availableNumbers) {
//         this.magicNumbers = availableNumbers;
//       } else {
//         this.magicNumbers += quantity;
//       }

//       this.handleAddMagicNumber();
//     },
//     decrementMagicNumber(quantity) {
//       if (this.magicNumbers - quantity < 0) {
//         this.magicNumbers = 0;
//       } else {
//         this.magicNumbers -= quantity;
//       }

//       this.handleAddMagicNumber();
//     },
//     handleAddMagicNumber() {
//       const freeNumbers = this.filteredNumbers.filter(
//         (n) => n.status === "FREE"
//       );

//       this.selectedNumbers = freeNumbers.slice(0, this.magicNumbers);

//       this.calcSaleDiscount();
//     },
//     getFirstFreeNumber() {
//       return this.filteredNumbers.find((n) => n.status === "FREE");
//     },
//     calcSaleDiscount() {
//       const { length } = this.selectedNumbers;

//       if (length === 0) {
//         this.cart.totals = 0;
//         return;
//       }

//       let partial = 0;

//       const sale = this.sales.find((s) => length >= s.quantity);

//       for (let i = 0; i < length; i++) {
//         if (sale !== undefined) {
//           partial += sale.price;
//           this.currentSale = { ...sale };
//         } else {
//           partial += this.contest.price;
//         }
//       }

//       this.cart.totals = partial;
//     },
//     isSelected(number) {
//       return !!this.selectedNumbers.find((n) => n.number === number.number);
//     },
//     handleScroll(el) {
//       if (
//         el.srcElement.offsetHeight + el.srcElement.scrollTop >=
//           el.srcElement.scrollHeight &&
//         this.current < this.quantity - this.partial
//       ) {
//         const filtered =
//           this.filter === "ALL"
//             ? [...this.numbers]
//             : [...this.numbers].filter((n) => n.status === this.filter);

//         this.current += this.partial;

//         this.filteredNumbers = [...filtered].splice(this.current, this.partial);
//       }
//     },
//   },
// });
</script>

<style>
.ant-skeleton-element,
.ant-skeleton-element .ant-skeleton-image {
  width: 100%;
}

.ant-skeleton-element {
  margin-top: 1rem;
}

.ant-skeleton-image {
  min-height: 400px;
}

.ant-progress-inner,
.ant-progress-bg {
  border-radius: 0;
}

.ant-progress-inner {
  background-color: #cfcfcf;
}
</style>