<template>
  <container>
    <contest-detail-skeleton :loading="loading">
      <a-row :gutter="[16, 16]">
        <a-col :xs="24" :lg="12" :xl="8" class="contest-detail-image">
          <a-image
            :preview="{ visible: false }"
            :src="
              contest && contest.gallery[0]
                ? contest.gallery[0].path
                : '/img/placeholder.jpg'
            "
            @click="showGallery = true"
          />
          <div style="display: none">
            <a-image-preview-group
              v-if="contest !== undefined"
              :preview="{
                visible: showGallery,
                onVisibleChange: (show) => (showGallery = show),
              }"
            >
              <a-image
                v-for="image in contest.gallery"
                :key="image.id"
                :src="image.path"
              />
            </a-image-preview-group>
          </div>
        </a-col>
        <a-col :xs="24" :lg="12" :xl="16" class="column-placeholder">
          <a-space direction="vertical" style="width: 100%">
            <a-typography-title :level="2">
              {{ contest && contest.title }}
            </a-typography-title>

            <a-typography-paragraph>
              {{ contest && contest.short_description }}
            </a-typography-paragraph>
          </a-space>

          <div class="contest-sales" v-if="contest && contest.sales.length > 0">
            <a-typography-title :level="4"> Promoções </a-typography-title>

            <a-list item-layout="horizontal" :data-source="contest.sales">
              <template #renderItem="{ item }">
                <a-list-item>
                  <a-list-item-meta
                    :description="`A partir de ${
                      item.quantity
                    } números cada um sai por ${formatPrice(item.price)}`"
                  >
                    <template #title>
                      <span
                        >De <del>{{ formatPrice(contest.price) }}</del> por
                        {{ formatPrice(item.price) }}</span
                      >
                    </template>
                  </a-list-item-meta>
                </a-list-item>
              </template>
            </a-list>
          </div>

          <a-typography-title :level="4"> Total vendido </a-typography-title>
          <div
            v-if="
              contest &&
              (contest.show_percentage || contest.use_custom_percentage)
            "
            :style="{ width: `${contestPercentage}%` }"
            class="contest-progress"
          >
            {{ contestPercentage }}%
          </div>

          <div class="contest-price">
            Participe desse sorteio por apenas
            <strong>{{ contestPrice }}</strong> por número.
          </div>

          <!-- Botão para o usuário visualizar os números do sorteio -->
          <!-- Botão de ir para o checkout -->
          <!-- Abrir modal de login simplificado caso não esteja logado -->
        </a-col>
      </a-row>

      <a-divider />

      <a-row :gutter="[16, 16]" v-if="contest !== undefined">
        <a-col :xs="24" :md="8" :lg="6">
          <a-typography-title :level="3"> Números mágicos </a-typography-title>
          <a-typography-paragraph>
            Escolha quantos números você quer comprar que o sistema vai escolher
            automáticamente para você
          </a-typography-paragraph>

          <a-space class="contest-numbers-count">
            <a-button type="primary" size="large" @click="decrement(1)">
              <minus-outlined />
            </a-button>
            <a-input
              v-model:value="order.quantity"
              type="number"
              size="large"
              :min="0"
              :max="contest.quantity"
            />
            <a-button type="primary" size="large" @click="increment(1)">
              <plus-outlined />
            </a-button>
          </a-space>
          <a-space style="margin: 10px auto">
            <a-button type="primary" @click="decrement(100)"> -100 </a-button>
            <a-button type="primary" @click="decrement(50)"> -50 </a-button>
            <a-button type="primary" @click="decrement(10)"> -10 </a-button>
            <a-button type="primary" @click="increment(10)"> +10 </a-button>
            <a-button type="primary" @click="increment(50)"> +50 </a-button>
            <a-button type="primary" @click="increment(100)"> +100 </a-button>
          </a-space>
        </a-col>
        <a-col
          :xs="24"
          :md="{ span: 8, offset: 8 }"
          :lg="{ span: 6, offset: 12 }"
          class="contest-checkout"
        >
          <a-typography-title :level="3"> Finalizar pedido </a-typography-title>

          <div class="description">
            <span class="label">Meus números</span>
            <span class="value"
              >{{ order.quantity }} x
              <span v-if="order.sale !== null">
                <del>{{ formatPrice(contest.price) }}</del>
                {{ formatPrice(order.sale.price) }}
              </span>
              <span v-else>
                {{ formatPrice(contest.price) }}
              </span>
            </span>
          </div>
          <div class="description">
            <span class="label">Total</span>
            <span class="value">{{ formatPrice(order.total) }}</span>
          </div>
          <a-button
            type="primary"
            size="large"
            block
            :disabled="order.quantity === 0"
            @click="finishOrder"
          >
            Finalizar compra
          </a-button>
        </a-col>
      </a-row>
    </contest-detail-skeleton>

    <simple-register-modal
      :visible="showSimpleLoginModal"
      @success="handleSuccess"
      @cancel="handleCancel"
    />
  </container>
</template>

<script lang="ts">
import ContestDetailSkeleton from "@/components/Contests/ContestDetailSkeleton.vue";
import Container from "@/components/_commons/Container.vue";
import { getContestBySlug } from "@/services/contests";
import { ContestDetail } from "@/types/Contest.types";
import { getErrorMessage } from "@/utils/handleError";
import { moneyFormat } from "@/utils/moneyFormat";
import {
  computed,
  defineComponent,
  onMounted,
  reactive,
  ref,
  watch,
} from "@vue/runtime-core";
import { notification } from "ant-design-vue";
import { useRoute, useRouter } from "vue-router";
import { MinusOutlined, PlusOutlined } from "@ant-design/icons-vue";
import { NumberStatusResponse } from "@/types/Number.types";
import { getContestNumbersStatus } from "@/services/numbers";
import { CartState, useCartStore } from "@/store/cart";
import SimpleRegisterModal from "@/components/Auth/SimpleRegisterModal.vue";
import { useAuthStore } from "@/store/auth";

export default defineComponent({
  components: {
    ContestDetailSkeleton,
    Container,
    SimpleRegisterModal,
    MinusOutlined,
    PlusOutlined,
  },
  name: "ContestDetail",
  setup() {
    const route = useRoute();
    const router = useRouter();
    const { user } = useAuthStore();
    const { saveCart } = useCartStore();
    const { username, slug } = route.params;
    const contest = ref<ContestDetail>();
    const loading = ref<boolean>(false);
    const showGallery = ref<boolean>(false);
    const showSimpleLoginModal = ref<boolean>(false);

    const numbersStatus = reactive<NumberStatusResponse>({
      total: 0,
      free: 0,
      reserved: 0,
      paid: 0,
    });

    const order = reactive<CartState>({
      contestId: 0,
      title: "",
      slug: "",
      username: username as string,
      description: "",
      whatsappNumber: "",
      whatsappGroup: "",
      price: 0,
      quantity: 0,
      sale: null,
      bankAccounts: [],
      total: 0,
    });

    const contestPrice = computed(() => {
      if (contest.value === undefined) return moneyFormat(0);

      const { price } = contest.value;

      return moneyFormat(price);
    });

    const contestPercentage = computed(() => {
      if (contest.value === undefined) return 0;

      const {
        show_percentage,
        use_custom_percentage,
        paid_percentage,
        custom_percentage,
      } = contest.value;
      let percentage = 0;

      if (show_percentage) percentage = paid_percentage * 100;

      if (use_custom_percentage) percentage = custom_percentage * 100;

      return percentage.toFixed(2);
    });

    async function getContestDetails() {
      try {
        loading.value = true;
        contest.value = await getContestBySlug(
          username as string,
          slug as string
        );
      } catch (error) {
        notification.error({
          message: getErrorMessage(error),
        });
      } finally {
        loading.value = false;
      }
    }

    async function getNumbersStatus(id: number) {
      try {
        loading.value = true;

        const result = await getContestNumbersStatus(id);

        numbersStatus.total = result.total;
        numbersStatus.free = result.free;
        numbersStatus.reserved = result.reserved;
        numbersStatus.paid = result.paid;
      } catch (error) {
        notification.error({
          message: getErrorMessage(error),
        });
      } finally {
        loading.value = false;
      }
    }

    function increment(value: number) {
      if (order.quantity + value > numbersStatus.free) {
        order.quantity = numbersStatus.free;
      } else {
        order.quantity += value;
      }
      calcDiscount();
    }

    function decrement(value: number) {
      if (order.quantity - value <= 0) {
        order.quantity = 0;
      } else {
        order.quantity -= value;
      }
      calcDiscount();
    }

    function calcDiscount() {
      if (contest.value === undefined) return;

      let partial = 0;
      const { sales, price } = contest.value;
      const contestSales = [...sales];
      const currentSale = contestSales
        .reverse()
        .find((sale) => order.quantity >= sale.quantity);

      for (let i = 0; i < order.quantity; i++) {
        if (currentSale !== undefined) {
          partial += currentSale.price;
          order.sale = { ...currentSale };
        } else {
          partial += price;
          order.sale = null;
        }
      }

      order.total = partial;
    }

    function finishOrder() {
      if (user === null) {
        showSimpleLoginModal.value = true;
        return;
      }

      router.push("/finalizar-compra");
    }

    function handleSuccess() {
      router.push("/finalizar-compra");
    }

    function handleCancel() {
      showSimpleLoginModal.value = false;
    }

    watch(contest, async (newVal) => {
      await getNumbersStatus(newVal.id);

      order.contestId = newVal.id;
      order.title = newVal.title;
      order.slug = newVal.slug;
      order.description = newVal.short_description;
      order.price = newVal.price;
      order.bankAccounts = newVal.bank_accounts;
      order.whatsappNumber = newVal.whatsapp_number;
      order.whatsappGroup = newVal.whatsapp_group;
    });

    watch(order, (newVal) => {
      saveCart({ ...newVal });
    });

    onMounted(async () => {
      await getContestDetails();
    });

    return {
      contest,
      loading,
      order,
      numbersStatus,
      showGallery,
      showSimpleLoginModal,
      contestPrice,
      contestPercentage,
      increment,
      decrement,
      finishOrder,
      formatPrice: (value: number) => moneyFormat(value),
      handleSuccess,
      handleCancel,
    };
  },
});
</script>

<style lang="scss">
.contest-detail-image {
  .ant-image,
  .ant-image-img {
    width: 100%;
    height: 400px;
    object-fit: cover;
  }
}
.contest-progress {
  background: red;
  color: #fff;
  font-weight: 500;
  padding: 1rem;
  text-align: right;
  margin-bottom: 1rem;
}
.contest-price {
  background: #22bb33;
  color: #fff;
  font-size: 18px;
  padding: 1rem;
  font-weight: 500;
  text-align: center;
}
.contest-numbers-count {
  width: 100%;
  .ant-space-item:nth-child(2) {
    flex-grow: 1;
  }
}
.contest-checkout {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  .description {
    display: flex;
    justify-content: space-between;
    font-size: 1.2rem;
    margin-bottom: 1rem;
    .label {
      font-weight: 600;
    }
    del {
      color: #888888;
    }
  }
}
</style>