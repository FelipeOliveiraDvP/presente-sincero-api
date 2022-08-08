<template>
  <container>
    <a-row :gutter="[16, 16]">
      <a-col :xs="24" :md="12" :lg="8">
        <a-card title="Resumo do pedido">
          <div class="summary-item">
            <strong>Sorteio</strong>
            <p>{{ title }}</p>
          </div>
          <div class="summary-item">
            <strong>Descrição</strong>
            <a-typography-paragraph
              :ellipsis="{ rows: 1 }"
              v-model:content="description"
            />
          </div>
          <div class="summary-item">
            <strong>Valor por número</strong>
            <p>{{ formatPrice(price) }}</p>
          </div>
          <div class="summary-item">
            <strong>Quantidade</strong>
            <p>{{ quantity }}</p>
          </div>
          <div class="summary-item">
            <strong>Promoção</strong>
            <p>{{ orderSale }}</p>
          </div>
          <div class="summary-item">
            <strong>Total</strong>
            <p v-if="sale !== null">
              De <del>{{ formatPrice(price * quantity) }}</del> por
              {{ formatPrice(total) }}
            </p>
            <p v-else>{{ formatPrice(total) }}</p>
          </div>
        </a-card>
      </a-col>
      <a-col :xs="24" :md="12" :lg="16">
        <a-spin :spinning="loading" size="large">
          <a-card title="Formas de pagamento">
            <a-row :gutter="[16, 16]">
              <a-col v-if="payment && payment.mercado_pago" :xs="24" :lg="12">
                <h3 style="display: flex; justify-content: space-between">
                  Pagar com Mercado Pago
                  <img
                    style="max-width: 96px"
                    src="/img/mercado-pago.png"
                    alt="Mercado Pago"
                  />
                </h3>

                <a-typography-text type="secondary">
                  Escaneie o QR Code abaixo ou copie o código para realizar o
                  pagamento. <br />
                  Após o pagamento, permaneça na página para visualizar o seu
                  comprovante.
                </a-typography-text>

                <a-result
                  v-if="confirmed"
                  status="success"
                  title="Seu pagamento foi confirmado com sucesso!"
                  :sub-title="`N° do pedido: #${
                    payment && payment.payment?.order_id
                  } Agora você precisa participar do grupo do sorteio do WhatsApp clicando no botão abaixo`"
                >
                  <template #extra>
                    <a :href="whatsappGroup" target="_blank">
                      <a-button key="group" type="primary">
                        <WhatsAppOutlined />
                        Participar do grupo</a-button
                      >
                    </a>
                    <a-button key="my_numbers">Ver meus números</a-button>
                  </template>
                </a-result>
                <a-card v-else style="max-width: 490px; margin: 10px auto">
                  <a-space
                    direction="vertical"
                    size="large"
                    style="max-width: 100%"
                  >
                    <img
                      style="max-width: 100%"
                      :src="`data:image/png;base64, ${
                        payment && payment.payment.qrcode_base64
                      }`"
                    />

                    <a-typography-paragraph
                      copyable
                      :v-model:content="
                        payment ? payment.payment.qr_code : null
                      "
                    >
                      {{ payment ? payment.payment.qr_code : null }}
                    </a-typography-paragraph>
                  </a-space>
                </a-card>
              </a-col>

              <a-col :xs="24" :lg="12">
                <h3 style="display: flex; justify-content: space-between">
                  Transferência bancária ou PIX
                </h3>

                <a-typography-text type="secondary">
                  Após realizar a trânsferencia, envie o comprovante do
                  pagamento para participar do sorteio.
                </a-typography-text>

                <a-row :gutter="[16, 16]" style="margin: 10px auto">
                  <a-col v-for="acc in bankAccounts" :key="acc.id" :xs="24">
                    <a-card v-if="acc.type === 'PIX'">
                      <template #title>
                        <h4
                          style="display: flex; justify-content: space-between"
                        >
                          {{ acc.name }}
                          <img
                            style="max-width: 72px"
                            src="/img/pix.png"
                            alt=""
                            srcset=""
                          />
                        </h4>
                      </template>
                      <div class="summary-item">
                        <strong>Chave</strong>
                        <a-typography-paragraph
                          copyable
                          :v-model:content="acc.key"
                        >
                          {{ acc.key }}
                        </a-typography-paragraph>
                      </div>
                    </a-card>
                    <a-card v-else>
                      <template #title>
                        <h4
                          style="display: flex; justify-content: space-between"
                        >
                          {{ acc.name }}
                          <BankOutlined />
                        </h4>
                      </template>
                      <div class="summary-item">
                        <strong>Conta corrente</strong>
                        <a-typography-paragraph
                          copyable
                          :v-model:content="acc.cc"
                        >
                          {{ acc.cc }}
                        </a-typography-paragraph>
                      </div>
                      <div class="summary-item">
                        <strong>Agência</strong>
                        <a-typography-paragraph
                          copyable
                          :v-model:content="acc.agency"
                        >
                          {{ acc.agency }}
                        </a-typography-paragraph>
                      </div>
                      <div class="summary-item">
                        <strong>Dígito verificador</strong>
                        <a-typography-paragraph
                          copyable
                          :v-model:content="acc.dv"
                        >
                          {{ acc.dv }}
                        </a-typography-paragraph>
                      </div>
                    </a-card>
                  </a-col>
                </a-row>

                <h3>Informações</h3>
                <a-typography-paragraph>
                  Em caso de dúvidas ou para enviar o comprovante do pagamento,
                  clique no botão abaixo para enviar a sua mensagem.
                </a-typography-paragraph>

                <a :href="whatsappLink" target="_blank">
                  <a-button type="primary" size="large" block>
                    <WhatsAppOutlined />
                    Falar com o vendedor
                  </a-button>
                </a>
              </a-col>
            </a-row>
          </a-card>
        </a-spin>
      </a-col>
    </a-row>

    <a-modal
      v-model:visible="showLeavingModal"
      title="Deseja realmente sair da página?"
      ok-text="Sim, sair da página"
      cancel-text="Cancelar"
      :okButtonProps="{ danger: true }"
      @ok="handleLeavePage"
    >
      <a-typography-paragraph>
        Ao sair da página, todas as informações do pedido serão perdidas.
      </a-typography-paragraph>
    </a-modal>
  </container>
</template>

<script lang="ts">
import Container from "@/components/_commons/Container.vue";
import { useCartStore } from "@/store/cart";
import { ReserveNumbersResponse } from "@/types/Number.types";
import { moneyFormat } from "@/utils/moneyFormat";
import {
  computed,
  defineComponent,
  onBeforeUnmount,
  onMounted,
  ref,
  toRefs,
  watch,
} from "@vue/runtime-core";
import { onBeforeRouteLeave, useRouter } from "vue-router";
import {
  CopyOutlined,
  DollarCircleOutlined,
  BankOutlined,
  WhatsAppOutlined,
} from "@ant-design/icons-vue";
import { useAuthStore } from "@/store/auth";
import { notification } from "ant-design-vue";
import { getErrorMessage } from "@/utils/handleError";
import { freeNumbers, reserveNumbers } from "@/services/numbers";

export default defineComponent({
  components: {
    Container,
    CopyOutlined,
    DollarCircleOutlined,
    BankOutlined,
    WhatsAppOutlined,
  },
  name: "Checkout",
  setup() {
    const cartStore = useCartStore();
    const authStore = useAuthStore();
    const router = useRouter();
    const {
      contestId,
      title,
      description,
      price,
      quantity,
      sale,
      bankAccounts,
      whatsappNumber,
      whatsappGroup,
      total,
      username,
      slug,
      hasOrder,
    } = toRefs(cartStore);
    const { user } = toRefs(authStore);
    const showLeavingModal = ref<boolean>(false);
    const loading = ref<boolean>(true);
    const leaving = ref<boolean>(true);
    const confirmed = ref<boolean>(false);
    const payment = ref<ReserveNumbersResponse>();

    const orderSale = computed(() => {
      return sale.value !== null
        ? `A cada ${sale.value.quantity} números cada um sai por ${moneyFormat(
            sale.value.price
          )}`
        : "Nenhuma";
    });

    const whatsappLink = computed(() => {
      const message = `Olá, meu nome é ${user.value.name}, e gostaria de conversar sobre o sorteio ${title.value}`;

      return `https://api.whatsapp.com/send?phone=55${
        whatsappNumber.value
      }&text=${encodeURI(message)}`;
    });

    async function getPaymentDetails() {
      try {
        loading.value = true;
        payment.value = await reserveNumbers(contestId.value, {
          random: quantity.value,
        });
      } catch (error) {
        notification.error({
          message: getErrorMessage(error),
        });
      } finally {
        loading.value = false;
      }
    }

    async function handleLeavePage() {
      try {
        await freeNumbers(contestId.value);
      } catch (error) {
        notification.error({
          message: getErrorMessage(error),
        });
      } finally {
        leaving.value = false;
        router.push(`/${username.value}/${slug.value}`);
      }
    }

    function showCloseWindowMessage(e: BeforeUnloadEvent) {
      e.returnValue = "";
    }

    watch(hasOrder, (newVal) => {
      if (newVal === false) {
        leaving.value = false;
        router.push("/");
      }
    });

    onMounted(async () => {
      if (hasOrder.value === false) {
        leaving.value = false;
        router.push("/");
        return;
      }

      await getPaymentDetails();

      window.Echo.channel("payment.confirmed").listen(
        "PaymentConfirmed",
        (e) => {
          confirmed.value =
            e.user_id === user.value.id &&
            e.order_id === payment.value.payment.order_id &&
            e.confirmed;
        }
      );

      window.addEventListener("beforeunload", showCloseWindowMessage);
    });

    onBeforeUnmount(() => {
      window.removeEventListener("beforeunload", showCloseWindowMessage);
    });

    onBeforeRouteLeave((_to, _from, next) => {
      if (leaving.value) {
        showLeavingModal.value = true;
        return;
      }
      next();
    });

    return {
      title,
      description,
      price,
      quantity,
      sale,
      orderSale,
      bankAccounts,
      total,
      showLeavingModal,
      payment,
      loading,
      confirmed,
      whatsappLink,
      whatsappGroup,
      handleLeavePage,
      formatPrice: (value: number) => moneyFormat(value),
    };
  },
});
</script>

<style>
</style>