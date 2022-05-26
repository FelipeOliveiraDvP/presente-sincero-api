<template>
  <b-container class="page">
    <div class="border p-4 my-4 rounded">
      <b-row>
        <b-col md="8" class="p-1">
          <h2>Finalizar compra</h2>

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
              <b-button variant="link" @click="addMoreNumbers">
                Gostaria de trocar ou escolher mais números? Clique aqui
              </b-button>
            </p>
          </div>

          <div class="d-flex justify-content-between w-50">
            <strong>Valor do número</strong>
            <p>{{ formatPrice(details.price) }}</p>
          </div>

          <div class="d-flex justify-content-between w-50">
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
        <b-col md="4">
          <!-- Pagamento confirmado -->
          <div v-if="paymentConfirmed" class="payment-success">
            <div class="icon text-success">
              <i class="fas fa-check-circle"></i>
            </div>
            <p>Pagamento recebido com sucesso. Boa sorte!</p>
            <router-link to="/sorteios">
              <b-button variant="primary">MAIS SORTEIOS</b-button>
            </router-link>
          </div>
          <!-- QRCode e código para copiar -->
          <div v-else>
            <my-loader v-if="loading" />
            <div v-else>
              <img
                src="/img/qr-code.png"
                alt="qrcode"
                class="img-fluid mb-2 rounded"
              />
              <b-input-group>
                <b-form-input type="text" readonly></b-form-input>

                <b-input-group-append>
                  <b-button variant="primary"
                    ><i class="fas fa-copy"></i
                  ></b-button>
                </b-input-group-append>
              </b-input-group>
            </div>
          </div>
        </b-col>
      </b-row>
    </div>
  </b-container>
</template>

<script>
import ContestNumberVue from "../components/Contests/ContestNumber.vue";
import LoaderVue from "../components/_commons/Loader.vue";
import moneyFormat from "../utils/moneyFormat";

export default {
  name: "Checkout",
  components: {
    "my-loader": LoaderVue,
    "contest-number": ContestNumberVue,
  },
  data() {
    return {
      loading: false,
      numbers: [],
      total: 0,
      details: {
        slug: "",
        short_description: "",
        price: 0,
      },
      paymentConfirmed: true,
    };
  },
  mounted() {
    const { numbers, total, details } = this.$router.history.current.params;

    this.numbers = numbers;
    this.total = total;
    this.details = { ...details };

    this.getQRCode();
  },
  methods: {
    async getQRCode() {
      this.loading = true;
      setTimeout(() => {
        this.loading = false;
      }, 2000);
    },
    addMoreNumbers() {
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
  },
};
</script>

<style>
</style>