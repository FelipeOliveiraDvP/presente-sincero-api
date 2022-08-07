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
        <a-card title="Formas de pagamento">
          <a-row :gutter="[16, 16]">
            <a-col v-if="true" :xs="24" :lg="12">
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

              <a-card v-if="false" style="max-width: 490px; margin: 10px auto">
                <a-space direction="vertical" size="large">
                  <img style="max-width: 100%" src="/img/placeholder.jpg" />

                  <a-input-group compact>
                    <a-input
                      v-model:value="payment"
                      style="width: calc(100% - 31px)"
                    />
                    <a-tooltip title="Copiar o código de pagamento">
                      <a-button type="primary">
                        <template #icon><CopyOutlined /></template>
                      </a-button>
                    </a-tooltip>
                  </a-input-group>
                </a-space>
              </a-card>
              <a-result
                v-else
                status="success"
                title="Seu pagamento foi confirmado com sucesso!"
                sub-title="N° do pedido: #123 Agora você precisa participar do grupo do sorteio do WhatsApp clicando no botão abaixo"
              >
                <template #extra>
                  <a :href="whatsappGroup" target="_blank">
                    <a-button key="group" type="primary">
                      <WhatsAppOutlined />
                      Participar do grupo</a-button
                    >
                  </a>
                  <a-button key="myNumbers">Ver meus números</a-button>
                </template>
              </a-result>
            </a-col>

            <a-col :xs="24" :lg="12">
              <h3 style="display: flex; justify-content: space-between">
                Transferência bancária ou PIX
              </h3>

              <a-typography-text type="secondary">
                Após realizar a trânsferencia, envie o comprovante do pagamento
                para participar do sorteio.
              </a-typography-text>

              <a-row :gutter="[16, 16]" style="margin: 10px auto">
                <a-col v-for="acc in bankAccounts" :key="acc.id" :xs="24">
                  <a-card v-if="acc.type === 'PIX'">
                    <template #title>
                      <h4 style="display: flex; justify-content: space-between">
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
                      <h4 style="display: flex; justify-content: space-between">
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
    const leaving = ref<boolean>(true);
    // const payment = ref<ReserveNumbersResponse>();
    const payment = ref<string>();

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

    function handleLeavePage() {
      leaving.value = false;
      router.push(`/${username.value}/${slug.value}`);
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

    onMounted(() => {
      if (hasOrder.value === false) {
        leaving.value = false;
        router.push("/");
        return;
      }
      console.log(bankAccounts.value);
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
      whatsappLink,
      whatsappGroup,
      handleLeavePage,
      formatPrice: (value: number) => moneyFormat(value),
    };
  },
});
// import ContestNumberVue from "@/components/Contests/ContestNumber.vue";
// import LoaderVue from "@/components/_commons/Loader.vue";
// import { moneyFormat } from "@/utils/moneyFormat";

// import { reserveNumbers, freeNumbers } from "@/services/numbers";

// export default {
//   name: "Checkout",
//   components: {
//     "my-loader": LoaderVue,
//     "contest-number": ContestNumberVue,
//   },
//   data() {
//     return {
//       loading: false,
//       leaving: false,
//       numbers: [],
//       total: 0,
//       details: {
//         slug: "",
//         short_description: "",
//         price: 0,
//       },
//       payment: {
//         order_id: null,
//         qrcode_base64: "",
//         qr_code: "",
//       },
//     };
//   },
//   created() {
//     const { numbers, total, details } = this.$route.params;

//     if (!numbers || !total || !details) {
//       this.$router.push({
//         name: "contests",
//       });
//       return;
//     }

//     this.numbers = numbers || [];
//     this.total = total || 0;
//     this.details = { ...details };
//     this.leaving = true;

//     window.addEventListener("beforeunload", this.showLeaveConfirmation);

//     this.getQRCode();
//   },
//   computed: {
//     paymentConfirmed() {
//       const { auth, payment } = this.$store.state;
//       const isCurrentUser = auth.user.id === payment.userId;
//       const isCurrentOrder = payment.orderId === this.payment.order_id;
//       const isConfirmed = payment.confirmed === true;

//       return isCurrentUser && isCurrentOrder && isConfirmed;
//     },
//   },
//   watch: {
//     paymentConfirmed(newVal, oldVal) {
//       if (newVal === true) {
//         this.leaving = false;
//       }
//     },
//   },
//   methods: {
//     async getQRCode() {
//       try {
//         this.loading = true;

//         const { contestId } = this.details;
//         const order = {
//           total: this.total,
//           numbers: this.numbers.map((n) => n.number),
//         };

//         const { payment } = await reserveNumbers(contestId, order);

//         this.payment = { ...payment };
//       } catch (error) {
//         console.error(error);

//         this.$router.push({
//           name: "contests",
//         });
//       } finally {
//         this.loading = false;
//       }
//     },
//     async freeReservedNumbers() {
//       try {
//         this.loading = true;

//         const { contestId } = this.details;
//         const order = {
//           numbers: this.numbers.map((n) => n.number),
//         };

//         await freeNumbers(contestId, order);

//         this.addMoreNumbers();
//       } catch (error) {
//         console.error(error);

//         this.$router.push({
//           name: "contests",
//         });
//       } finally {
//         this.loading = false;
//       }
//     },
//     showLeaveConfirmation(e) {
//       e.returnValue = "";
//     },
//     addMoreNumbers() {
//       this.leaving = false;
//       this.$router.push({
//         name: "contestDetail",
//         params: {
//           slug: this.details.slug,
//           checkout: {
//             totals: this.total,
//             numbers: this.numbers,
//           },
//         },
//       });
//     },
//     formatPrice(price) {
//       return moneyFormat(price);
//     },
//     onCopySuccess(e) {
//       this.$toasted.show("Código copiado com sucesso!", {
//         type: "success",
//         theme: "toasted-primary",
//         position: "top-right",
//         duration: 3000,
//       });
//     },
//     onCopyError(e) {
//       this.$toasted.show("Erro ao copiar o código", {
//         type: "error",
//         theme: "toasted-primary",
//         position: "top-right",
//         duration: 3000,
//       });
//     },
//   },
//   beforeRouteLeave(to, from, next) {
//     if (this.leaving) {
//       // this.$bvModal.show("confirmation-leave-modal");
//       return;
//     }

//     next();
//   },
//   beforeDestroy() {
//     window.removeEventListener("beforeunload", this.showLeaveConfirmation);
//   },
// };
</script>

<style>
</style>