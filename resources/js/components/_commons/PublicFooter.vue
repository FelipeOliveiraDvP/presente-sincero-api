<template>
  <footer class="public-footer">
    <b-container>
      <b-row>
        <b-col md="6" lg="3">
          <h3>Mapa do site</h3>
          <router-link to="/termos-de-uso">Termos de uso</router-link>
        </b-col>
        <b-col md="6" lg="3">
          <h3>Formas de pagamento</h3>
          <div>
            <img src="/img/pix.png" alt="Pague com PIX" />
            <img src="/img/mercado-pago.png" alt="Mercado Pago" />
          </div>
        </b-col>
        <b-col md="6" lg="3">
          <h3>Segurança</h3>
          <div class="d-flex flex-column justify-content-center">
            <img src="/img/site-seguro.png" alt="Site seguro" />
            <img src="/img/federal.png" alt="Loteria Federal" />
          </div>
        </b-col>
        <b-col md="6" lg="3">
          <h3>Redes sociais</h3>
          <ul class="social-list list-unstyled d-flex w-100">
            <li>
              <a href="#" target="_blank"><i class="fab fa-instagram"></i></a>
            </li>
            <li>
              <a href="#" target="_blank"><i class="fab fa-facebook"></i></a>
            </li>
            <li>
              <a href="#" target="_blank"><i class="fab fa-whatsapp"></i></a>
            </li>
          </ul>
        </b-col>
      </b-row>
    </b-container>
    <p class="copyright bg-dark text-white text-right p-3">
      <b-container> 2022 - Todos os direitos reservados </b-container>
    </p>
  </footer>
</template>

<script>
export default {};
</script>

<style>
</style>