<template>
  <b-container fluid>
    <h2>Gerenciar sorteio</h2>
    <b-row class="text-center" style="font-size: 32px">
      <b-col md="4"
        ><b-card bg-variant="success">
          <h3>Disponível</h3>
          {{ countNumbersByStatus("FREE") }}
        </b-card></b-col
      >
      <b-col md="4"
        ><b-card bg-variant="warning"
          ><h3>Reservado</h3>
          {{ countNumbersByStatus("RESERVED") }}</b-card
        ></b-col
      >
      <b-col md="4"
        ><b-card bg-variant="danger"
          ><h3>Pago</h3>
          {{ countNumbersByStatus("PAID") }}</b-card
        ></b-col
      >
    </b-row>

    <div class="mt-4">
      <b-row>
        <b-col md="6">
          <b-button-group class="w-100">
            <b-button @click="handleFilterNumbers('ALL')" variant="light"
              >Todos ({{ numbers.length }})</b-button
            >
            <b-button @click="handleFilterNumbers('FREE')" variant="success"
              >Disponível ({{ countNumbersByStatus("FREE") }})</b-button
            >
            <b-button @click="handleFilterNumbers('RESERVED')" variant="warning"
              >Reservados ({{ countNumbersByStatus("RESERVED") }})</b-button
            >
            <b-button @click="handleFilterNumbers('PAID')" variant="danger"
              >Pagos ({{ countNumbersByStatus("PAID") }})</b-button
            >
          </b-button-group>
        </b-col>
        <b-col md="6" lg="4" offset-lg="2">
          <!-- <b-button
            v-b-modal.customer-numbers
            variant="secondary"
            class="d-block w-100 mt-2 mt-md-0"
            >MEUS NÚMEROS</b-button
          > -->
        </b-col>
      </b-row>
    </div>

    <div
      class="my-4 border border-secondary p-4"
      style="max-height: 400px; overflow-y: auto"
    >
      <b-row>
        <b-col
          :key="number.number"
          v-for="number in filteredNumbers"
          cols="4"
          md="2"
          lg="1"
          class="p-2 text-center"
        >
          <contest-number
            :number="number"
            :isSelected="isSelected(number)"
            @select="handleSelectNumber"
          />
        </b-col>
      </b-row>
    </div>

    <div class="my-4">
      <b-row>
        <b-col md="6" lg="8">
          <h4>Números selecionados</h4>
          <b-row style="max-height: 120px; overflow-y: auto">
            <b-col cols="12" v-if="selectedNumbers.length === 0">
              Você ainda não escolheu nenhum número
            </b-col>
            <b-col
              :key="selected.number"
              v-for="selected in selectedNumbers"
              cols="6"
              md="4"
              lg="3"
              class="p-2 text-center"
            >
              <contest-number
                :number="selected"
                allowRemove
                @remove="handleRemoveSelectedNumber"
              />
            </b-col>
          </b-row>
        </b-col>
        <b-col md="6" lg="4">
          <b-button
            variant="success"
            size="lg"
            :disabled="selectedNumbers.length === 0"
          >
            Marcar números como disponível
          </b-button>
        </b-col>
      </b-row>
    </div>
  </b-container>
</template>

<script>
import ContestNumberVue from "../../components/Contests/ContestNumber.vue";
export default {
  name: "AdminContestManage",
  components: {
    "contest-number": ContestNumberVue,
  },
  data() {
    return {
      loading: false,
      numbers: [],
      selectedNumbers: [],
      filteredNumbers: [],
    };
  },
  mounted() {
    this.numbers = this.getNumbers();
    this.filteredNumbers = this.numbers;
  },
  methods: {
    getNumbers() {
      const STATUS = ["FREE", "RESERVED", "PAID"];
      const n = [];

      for (let i = 0; i < 300; i++) {
        n.push({
          number: i,
          status: STATUS[Math.floor(Math.random() * 3)],
          customer: {
            id: i + 1,
            name: `Cliente ${i + 1}`,
            whatsapp: `(11) 99999 4444`,
          },
        });
      }

      return n;
    },
    handleSelectNumber(number) {
      const isSelected = !!this.selectedNumbers.find(
        (n) => n.number === number.number
      );

      if (!isSelected && number.status === "RESERVED") {
        this.selectedNumbers.push(number);
      }
    },
    handleRemoveSelectedNumber(number) {
      const index = this.selectedNumbers.indexOf(number);

      this.selectedNumbers.splice(index, 1);
    },
    handleFilterNumbers(filter) {
      this.filter = filter;

      if (filter === "ALL") {
        this.filteredNumbers = this.numbers;
      } else {
        const filtered = this.numbers.filter((n) => n.status === filter);

        this.filteredNumbers = filtered;
      }
    },
    countNumbersByStatus(status) {
      const filtered = this.numbers.filter((n) => n.status === status);

      return filtered.length;
    },
    isSelected(number) {
      return !!this.selectedNumbers.find((n) => n.number === number.number);
    },
  },
};
</script>

<style>
</style>