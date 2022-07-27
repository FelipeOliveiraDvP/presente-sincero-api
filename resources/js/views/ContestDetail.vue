<template>
  <container>
    <h1>Detalhes do sorteio</h1>
    <!-- <contest-detail-skeleton :loading="loading || contest === null">
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
          <a-space direction="vertical">
            <p>{{ contest.short_description }}</p>

            <div
              v-if="contest.show_percentage || contest.use_custom_percentage"
            >
              <a-typography-title :level="4"
                >Números vendidos</a-typography-title
              >
              <a-progress :stroke-width="40" :percent="contestPercentage" />
            </div>

            <div></div>
          </a-space>
        </a-col>
      </a-row>     
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
    </contest-detail-skeleton> -->
  </container>
</template>

<script lang="ts">
import { computed, defineComponent, onMounted, reactive, ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import { notification } from "ant-design-vue";
import { useStore } from "vuex";

import Container from "@/components/_commons/Container.vue";
import ContestNumbersModal from "@/components/Customers/ContestNumbersModal.vue";
import ContestNumbersList from "@/components/Contests/ContestNumbersList.vue";
import ContestDetailSkeleton from "@/components/Contests/ContestDetailSkeleton.vue";
import SimpleRegisterModal from "@/components/Auth/SimpleRegisterModal.vue";

import { getContestBySlug } from "@/services/contests";

import { moneyFormat } from "@/utils/moneyFormat";
import { contestPercentage as percentage } from "@/utils/contestPercentage";

export default defineComponent({
  name: "ContestDetail",
  // setup() {
  //   const visible = ref(false);
  //   const loading = ref(false);
  //   const showNumbersModal = ref(false);
  //   const filter = ref("ALL");
  //   const sales = ref([]);
  //   const numbers = ref([]);
  //   const selectedNumbers = ref([]);
  //   const filteredNumbers = ref([]);
  //   const quantity = ref(0);
  //   const magicNumbers = ref(0);
  //   const contest = ref({
  //     id: 0,
  //     user_id: 0,
  //     winner_id: null,
  //     title: "",
  //     slug: "",
  //     start_date: "",
  //     max_reserve_days: 1,
  //     show_percentage: false,
  //     use_custom_percentage: false,
  //     paid_percentage: 0,
  //     custom_percentage: 0,
  //     contest_date: null,
  //     price: 0,
  //     quantity: 0,
  //     short_description: "",
  //     full_description: "",
  //     whatsapp_number: "",
  //     whatsapp_group: "",
  //     numbers: "[]",
  //     status: "ACTIVE",
  //     created_at: "",
  //     updated_at: "",
  //     deleted_at: null,
  //     seller: {
  //       id: 0,
  //       name: "",
  //       username: "",
  //     },
  //     gallery: null,
  //     bank_accounts: [],
  //     sales: [],
  //   });

  //   const currentSale = reactive({
  //     quantity: 0,
  //     price: 0,
  //   });

  //   const cart = reactive({
  //     totals: 0,
  //   });

  //   const formattedCartTotal = computed(() => moneyFormat(cart.totals));
  //   const contestPercentage = computed(() => percentage(contest));
  //   const hasSale = computed(() =>
  //     sales.value.find((sale) => selectedNumbers.value.length >= sale.quantity)
  //   );

  //   const route = useRoute();
  //   const router = useRouter();
  //   const store = useStore();

  //   async function getContestData() {
  //     try {
  //       loading.value = true;

  //       const { username, slug } = route.params;

  //       const result = await getContestBySlug(username, slug);
  //       const numbersArray = result.numbers ? JSON.parse(result.numbers) : [];

  //       contest.value = { ...result };
  //       quantity.value = result.quantity;
  //       numbers.value = numbersArray.map((number) => JSON.parse(number));
  //       filteredNumbers.value = [...numbers.value];
  //       magicNumbers.value = selectedNumbers.value.length;
  //       sales.value = result.sales
  //         .map((sale) => ({
  //           price: sale.price,
  //           quantity: sale.quantity,
  //         }))
  //         .reverse();

  //       calcSaleDiscount();
  //     } catch (error) {
  //       notification.error({
  //         message: error.message,
  //       });
  //     } finally {
  //       loading.value = false;
  //     }
  //   }

  //   function calcSaleDiscount() {
  //     const { length } = selectedNumbers.value;

  //     if (length === 0) {
  //       cart.totals = 0;
  //       return;
  //     }

  //     let partial = 0;

  //     const sale = sales.value.find((s) => length >= s.quantity);

  //     for (let i = 0; i < length; i++) {
  //       if (sale !== undefined) {
  //         partial += sale.price;

  //         currentSale.quantity = sale.quantity;
  //         currentSale.price = sale.price;
  //       } else {
  //         partial += contest.value.price;
  //       }
  //     }

  //     cart.totals = partial;
  //   }

  //   function formatPrice(value) {
  //     return moneyFormat(value);
  //   }

  //   // List
  //   function countNumbersByStatus(status) {
  //     const filtered = numbers.value.filter((n) => n.status === status);

  //     return filtered.length;
  //   }

  //   function incrementMagicNumber(quantity) {
  //     const availableNumbers = countNumbersByStatus("FREE");

  //     if (magicNumbers.value + quantity > availableNumbers) {
  //       magicNumbers.value = availableNumbers;
  //     } else {
  //       magicNumbers.value += quantity;
  //     }

  //     addMagicNumbers();
  //   }

  //   function decrementMagicNumber(quantity) {
  //     if (magicNumbers.value - quantity < 0) {
  //       magicNumbers.value = 0;
  //     } else {
  //       magicNumbers.value -= quantity;
  //     }

  //     addMagicNumbers();
  //   }

  //   function addMagicNumbers() {
  //     const freeNumbers = filteredNumbers.value.filter(
  //       (n) => n.status === "FREE"
  //     );

  //     selectedNumbers.value = freeNumbers.slice(0, magicNumbers.value);

  //     calcSaleDiscount();
  //   }

  //   // List
  //   function isSelected(number) {
  //     return !!selectedNumbers.value.find((n) => n.number === number.number);
  //   }

  //   function handleSelectNumber(number) {
  //     const isSelected = !!selectedNumbers.value.find(
  //       (n) => n.number === number.number
  //     );

  //     if (!isSelected && number.status === "FREE") {
  //       selectedNumbers.value = [...selectedNumbers.value, number];
  //       calcSaleDiscount();
  //     }
  //   }

  //   function handleRemoveSelectedNumber(number) {
  //     const index = selectedNumbers.value.indexOf(number);
  //     const newArray = [...selectedNumbers.value].splice(index, 1);

  //     selectedNumbers.value = [...newArray];

  //     calcSaleDiscount();
  //   }

  //   function handleFilterNumbers(current) {
  //     filter.value = current;

  //     if (filter === "ALL") {
  //       filteredNumbers.value = [...numbers.value];
  //     } else {
  //       const filtered = numbers.value.filter((n) => n.status === current);

  //       filteredNumbers.value = [...filtered];
  //     }
  //   }

  //   function handleCheckout() {
  //     const { authenticated } = store.state.auth;

  //     if (!authenticated) {
  //       // TODO: Mostrar modal de login simples
  //       return;
  //     }

  //     router.push({
  //       name: "checkout",
  //       params: {
  //         numbers: selectedNumbers.value,
  //         total: cart.totals,
  //         details: {
  //           contestId: contest.value.id,
  //           title: contest.value.title,
  //           slug: contest.value.slug,
  //           seller: contest.value.seller,
  //           price: contest.value.price,
  //           short_description: contest.value.short_description,
  //           whatsapp_number: contest.value.whatsapp_number,
  //           whatsapp_group: contest.value.whatsapp_group,
  //           sale: currentSale,
  //         },
  //       },
  //     });
  //   }

  //   onMounted(async () => {
  //     await getContestData();
  //   });

  //   return {
  //     visible,
  //     loading,
  //     showNumbersModal,
  //     filter,
  //     contest,
  //     sales,
  //     numbers,
  //     selectedNumbers,
  //     filteredNumbers,
  //     quantity,
  //     magicNumbers,
  //     currentSale,
  //     cart,
  //     formattedCartTotal,
  //     hasSale,
  //     contestPercentage,
  //     formatPrice,
  //     incrementMagicNumber,
  //     decrementMagicNumber,
  //     isSelected,
  //     handleSelectNumber,
  //     handleRemoveSelectedNumber,
  //     handleCheckout,
  //     handleFilterNumbers,
  //   };
  // },
  components: {
    Container,
    ContestNumbersList,
    ContestNumbersModal,
    SimpleRegisterModal,
    ContestDetailSkeleton,
  },
});
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