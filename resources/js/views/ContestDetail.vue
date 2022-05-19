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
            COMPRE AGORA POR R$ 5,00
          </div>
        </div>
      </b-col>
    </b-row>

    <div class="mt-4">
      <b-button-group>
        <b-button variant="success">Disponível</b-button>
        <b-button variant="warning">Reservados</b-button>
        <b-button variant="danger">Pagos</b-button>
      </b-button-group>

      <b-button variant="secondary">MEUS NÚMEROS</b-button>
    </div>

    <div
      class="my-4 border border-secondary p-4"
      style="max-height: 400px; overflow-y: auto"
    >
      <b-row>
        <contest-number :key="i" v-for="i in 1200" :value="i" />
      </b-row>
    </div>

    <div class="border p-4 mb-4">
      <b-row>
        <b-col md="6" lg="4">
          <h4>Números selecionados</h4>
          <b-row style="max-height: 120px; overflow-y: auto">
            <b-col
              :key="i"
              v-for="i in 12"
              cols="4"
              class="border p-1 text-center"
            >
              {{ i }}
            </b-col>
          </b-row>
        </b-col>
        <b-col md="6" lg="4">
          <div class="d-flex justify-content-center align-items-center h-100">
            <p>3 X R$ 5,00</p>
          </div>
        </b-col>
        <b-col md="6" lg="4">
          <div class="d-flex justify-content-center align-items-center h-100">
            <router-link to="/finalizar-compra">
              <b-button variant="secondary" size="lg">
                COMPRAR NÚMEROS
              </b-button>
            </router-link>
          </div>
        </b-col>
      </b-row>

      <div></div>
    </div>
  </b-container>
</template>

<script>
import ContestNumberVue from "../components/Contests/ContestNumber.vue";

export default {
  name: "ContestDetail",
  components: {
    "contest-number": ContestNumberVue,
  },
};
</script>

<style>
</style>