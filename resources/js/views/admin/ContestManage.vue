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
      <my-loader v-if="loading" />
      <b-row v-else>
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
      @scroll="handleScroll"
    >
      <my-loader v-if="loading" />
      <b-row v-else>
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
      <my-loader v-if="loading" />
      <contest-percentage-form
        v-else
        class="mb-4"
        :percentageInfo="percentageInfo"
        @changePercentage="handleChangePercentage"
      />
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
            @click="handleFreeNumbers"
          >
            Marcar números como disponível
          </b-button>
        </b-col>
      </b-row>
    </div>
  </b-container>
</template>

<script>
import LoaderVue from "@/components/_commons/Loader.vue";
import ContestNumberVue from "@/components/Contests/ContestNumber.vue";
import ContestPercentageForm from "@/components/Contests/Admin/ContestPercentageForm.vue";

import { getContest, editContest } from "@/services/contests";
import { freeNumbers } from "@/services/numbers";

export default {
  name: "AdminContestManage",
  components: {
    "my-loader": LoaderVue,
    "contest-number": ContestNumberVue,
    "contest-percentage-form": ContestPercentageForm,
  },
  data() {
    return {
      loading: false,
      contestId: null,
      numbers: [],
      selectedNumbers: [],
      filteredNumbers: [],
      quantity: 0,
      partial: 300,
      current: 300,
      // os filteredNumbers iniciais, vão receber os numeros parciais
      // quando chegar no final do scroll, os filteredNumbers vão receber mais parciais
      percentageInfo: {
        show_percentage: false,
        use_custom_percentage: false,
        paid_percentage: 0,
        custom_percentage: 0,
      },
    };
  },
  mounted() {
    const { id } = this.$router.history.current.params;

    this.contestId = id;
    this.getContestData(id);
  },
  methods: {
    async getContestData(id) {
      try {
        this.loading = true;

        const result = await getContest(id);
        const numbersArray = result.numbers ? JSON.parse(result.numbers) : [];

        this.quantity = result.quantity;
        this.numbers = numbersArray.map((number) => JSON.parse(number));
        this.filteredNumbers = [...this.numbers].splice(0, this.partial);
        this.current += this.partial;
        this.percentageInfo = {
          show_percentage: result.show_percentage,
          use_custom_percentage: result.use_custom_percentage,
          paid_percentage: result.paid_percentage,
          custom_percentage: result.custom_percentage,
        };
      } catch (error) {
        return [];
      } finally {
        this.loading = false;
      }
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
    // TODO: Fitrar pelo endpoint de numbers
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
    async handleChangePercentage(obj) {
      try {
        this.loading = true;
        let result;

        if (obj.selected === "use_true") {
          result = await editContest(this.contestId, {
            show_percentage: true,
            use_custom_percentage: false,
          });
        }

        if (obj.selected === "use_custom") {
          result = await editContest(this.contestId, {
            show_percentage: false,
            use_custom_percentage: true,
            custom_percentage: obj.percentage,
          });
        }

        this.$toasted.show(result.message, {
          type: "success",
          theme: "toasted-primary",
          position: "top-right",
          duration: 3000,
        });
      } catch (error) {
        this.$toasted.show(error.message, {
          type: "error",
          theme: "toasted-primary",
          position: "top-right",
          duration: 3000,
        });
      } finally {
        this.loading = false;
      }
    },
    handleFreeNumbers() {
      console.log("Liberar números selecionados");
    },
    handleScroll(el) {
      if (
        el.srcElement.offsetHeight + el.srcElement.scrollTop >=
        el.srcElement.scrollHeight
      ) {
        this.filteredNumbers = [...this.numbers].splice(0, this.current);
        this.current += this.partial;
      }
    },
  },
};
</script>

<style>
</style>