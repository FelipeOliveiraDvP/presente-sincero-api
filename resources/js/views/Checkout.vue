<template>
  <b-container class="page">
    <div class="border p-4 my-4 bg-white">
      <b-row>
        <b-col md="6">
          <h2>Finaliza compra</h2>

          <div class="d-flex justify-content-between">
            <p>Prêmio do sorteio</p>
            <p>
              Lorem ipsum dolor, sit amet consectetur adipisicing elit.
              Similique
            </p>
          </div>

          <div class="d-flex justify-content-between">
            <p>Meus números</p>
            <p>
              <b-row style="max-height: 60px; overflow-y: auto">
                <b-col
                  :key="i"
                  v-for="i in 12"
                  cols="4"
                  class="border p-1 text-center"
                >
                  {{ i }}
                </b-col>
              </b-row>
            </p>
          </div>

          <div class="d-flex justify-content-between">
            <p>Valor do número</p>
            <p>R$ 5,00</p>
          </div>

          <div class="d-flex justify-content-between">
            <p>Valor total</p>
            <p>R$ 15,00</p>
          </div>

          <p>
            <strong>Pague com PIX:</strong>
            Escaneie o QR Code ao lado com seu celular, ou copie o código para
            fazer o pagamento
          </p>

          <p>
            Após o pagamento, permaneça na página para aguardar a confirmação.
          </p>
        </b-col>
        <b-col md="6">
          <div v-if="paymentConfirmed" class="payment-success">
            <div class="icon text-success">
              <i class="fas fa-check-circle"></i>
            </div>
            <p>Pagamento recebido com sucesso. Boa sorte!</p>
            <router-link to="/sorteios">
              <b-button variant="primary">MAIS SORTEIOS</b-button>
            </router-link>
          </div>
          <div v-else>
            <img src="/img/qr-code.png" alt="qrcode" class="img-fluid" />
            <b-input-group>
              <b-form-input type="text" readonly></b-form-input>

              <b-input-group-append>
                <b-button variant="primary"
                  ><i class="fas fa-copy"></i
                ></b-button>
              </b-input-group-append>
            </b-input-group>
          </div>
        </b-col>
      </b-row>
    </div>
  </b-container>
</template>

<script>
export default {
  name: "Checkout",
  data() {
    return {
      paymentConfirmed: true,
    };
  },
};
</script>

<style>
</style>