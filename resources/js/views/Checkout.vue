<template>
  <b-container class="page">
    <div class="border p-4 my-4 rounded">
      <b-row>
        <b-col md="7" class="p-1">
          <h2>Finalizar compra</h2>

          <div class="">
            <strong>Sorteio</strong>
            <p>{{ details.title }}</p>
          </div>

          <div class="">
            <strong>Prêmio do sorteio</strong>
            <p>{{ details.short_description }}</p>
          </div>

          <div>
            <p><strong>Meus números</strong></p>
            <p>
              <b-row style="max-height: 120px; overflow-y: auto">
                <b-col
                  :key="number.number"
                  v-for="number in numbers"
                  cols="4"
                  md="3"
                  lg="2"
                  class="p-1 text-center"
                >
                  <contest-number :number="number" />
                </b-col>
              </b-row>
            </p>
            <p>
              <b-button
                variant="link"
                @click="$bvModal.show('confirmation-leave-modal')"
              >
                Gostaria de trocar ou escolher mais números? Clique aqui
              </b-button>
            </p>
          </div>

          <div class="d-flex justify-content-between w-md-50">
            <strong>Quantidade</strong>
            <p>{{ numbers.length }}</p>
          </div>

          <div class="d-flex justify-content-between w-md-50">
            <strong>Valor do número</strong>
            <p v-if="details.sale.quantity > 0">
              <del>{{ formatPrice(details.price) }}</del>
              {{ formatPrice(details.sale.price) }}
            </p>
            <p v-else>{{ formatPrice(details.price) }}</p>
          </div>

          <div class="d-flex justify-content-between w-md-50">
            <strong>Valor total</strong>
            <p>{{ formatPrice(total) }}</p>
          </div>

          <div class="divider"></div>

          <p>
            <strong>Pague com PIX:</strong>
            Escaneie o QR Code ao lado com seu celular, ou copie o código para
            fazer o pagamento
          </p>

          <p>
            Após o pagamento, permaneça na página para aguardar a confirmação.
          </p>
        </b-col>
        <b-col md="5" class="d-flex justify-content-center align-items-center">
          <!-- Pagamento confirmado -->
          <div v-if="paymentConfirmed" class="payment-success">
            <div class="icon text-success">
              <i class="fas fa-check-circle"></i>
            </div>
            <p>Pagamento recebido com sucesso. Boa sorte!</p>
            <router-link :to="`/sorteios/${this.details.slug}`">
              <b-button variant="primary">Escolher mais números</b-button>
            </router-link>
          </div>
          <!-- QRCode e código para copiar -->
          <div v-else>
            <my-loader v-if="loading" />
            <div class="text-center" v-else>
              <img
                src="/img/mercado-pago.png"
                class="w-50 mb-2"
                alt="Mercado Pago"
              />
              <img
                :src="`data:image/jpeg;base64,${payment.qrcode_base64}`"
                alt="qrcode"
                class="img-fluid mb-2 rounded"
              />
              <b-input-group>
                <b-form-input
                  type="text"
                  v-model="payment.qr_code"
                  readonly
                ></b-form-input>

                <b-input-group-append>
                  <b-button
                    variant="primary"
                    v-clipboard:copy="payment.qr_code"
                    v-clipboard:success="onCopySuccess"
                    v-clipboard:error="onCopyError"
                    ><i class="fas fa-copy"></i
                  ></b-button>
                </b-input-group-append>
              </b-input-group>
            </div>
          </div>
        </b-col>
      </b-row>
    </div>

    <!-- Confirmation modal -->
    <b-modal
      id="confirmation-leave-modal"
      ok-variant="danger"
      ok-title="Sim, desejo sair"
      cancel-title="Não"
      @ok="freeReservedNumbers"
    >
      <template #modal-title> Deseja realmente sair da página? </template>
      <div>
        <p>
          Ao sair da página, seus números selecionados serão disponibilizados
          para outros clientes.
        </p>
        <p>Recomendamos finalizar a compra para garantir seus números.</p>
        <p><strong>Ainda sim deseja sair?</strong></p>
      </div>
    </b-modal>
  </b-container>
</template>

<script>
import ContestNumberVue from "@/components/Contests/ContestNumber.vue";
import LoaderVue from "@/components/_commons/Loader.vue";
import moneyFormat from "@/utils/moneyFormat";

import { reserveNumbers, freeNumbers } from "@/services/numbers";

export default {
  name: "Checkout",
  components: {
    "my-loader": LoaderVue,
    "contest-number": ContestNumberVue,
  },
  data() {
    return {
      loading: false,
      leaving: false,
      numbers: [],
      total: 0,
      details: {
        slug: "",
        short_description: "",
        price: 0,
      },
      payment: {
        qrcode_base64: "",
        qr_code: "",
      },
      paymentConfirmed: false,
    };
  },
  created() {
    const { numbers, total, details } = this.$router.history.current.params;

    if (!numbers || !total || !details) {
      this.$router.push({
        name: "contests",
      });
      return;
    }

    this.numbers = numbers || [];
    this.total = total || 0;
    this.details = { ...details };
    this.leaving = true;

    window.addEventListener("beforeunload", this.showLeaveConfirmation);
  },
  mounted() {
    this.getQRCode();
  },
  methods: {
    async getQRCode() {
      try {
        this.loading = true;

        const { contestId } = this.details;
        const order = {
          total: this.total,
          numbers: this.numbers.map((n) => n.number),
        };

        const { payment } = await reserveNumbers(contestId, order);

        this.payment = { ...payment };
      } catch (error) {
        this.$toasted.show(error.message, {
          type: "error",
          theme: "toasted-primary",
          position: "top-right",
          duration: 3000,
        });

        this.$router.push({
          name: "contests",
        });
      } finally {
        this.loading = false;
      }
    },
    async freeReservedNumbers() {
      try {
        this.loading = true;

        const { contestId } = this.details;
        const order = {
          numbers: this.numbers.map((n) => n.number),
        };

        await freeNumbers(contestId, order);

        this.addMoreNumbers();
      } catch (error) {
        this.$toasted.show(error.message, {
          type: "error",
          theme: "toasted-primary",
          position: "top-right",
          duration: 3000,
        });

        this.$router.push({
          name: "contests",
        });
      } finally {
        this.loading = false;
      }
    },
    showLeaveConfirmation(e) {
      e.returnValue = "";
    },
    addMoreNumbers() {
      this.leaving = false;
      this.$router.push({
        name: "contestDetail",
        params: {
          slug: this.details.slug,
          checkout: {
            totals: this.total,
            numbers: this.numbers,
          },
        },
      });
    },
    formatPrice(price) {
      return moneyFormat(price);
    },
    onCopySuccess(e) {
      this.$toasted.show("Código copiado com sucesso!", {
        type: "success",
        theme: "toasted-primary",
        position: "top-right",
        duration: 3000,
      });
    },
    onCopyError(e) {
      this.$toasted.show("Erro ao copiar o código", {
        type: "error",
        theme: "toasted-primary",
        position: "top-right",
        duration: 3000,
      });
    },
  },
  beforeRouteLeave(to, from, next) {
    if (this.leaving) {
      this.$bvModal.show("confirmation-leave-modal");
      return;
    }

    next();
  },
  beforeDestroy() {
    window.removeEventListener("beforeunload", this.showLeaveConfirmation);
  },
};
</script>

<style>
</style>