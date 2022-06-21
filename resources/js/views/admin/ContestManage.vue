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
          <!-- TODO: Filtros de nome e whatsapp -->
          <users-select
            @select="handleFilterByCustomer"
            @clear="handleClearFilterByCustomer"
          />
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
            variant="danger"
            size="lg"
            class="mb-2 w-100"
            :disabled="customerId === null"
            @click="$bvModal.show('confirmation-paid-modal')"
          >
            Marcar selecionados como pago
          </b-button>
          <b-button
            variant="success"
            size="lg"
            class="w-100"
            @click="$bvModal.show('confirmation-free-modal')"
          >
            Marcar reservados como disponível
          </b-button>
        </b-col>
      </b-row>

      <!-- Andamento do sorteio -->
      <my-loader v-if="loading" />
      <contest-percentage-form
        v-else
        class="my-4"
        :percentageInfo="percentageInfo"
        @changePercentage="handleChangePercentage"
      />
    </div>

    <!-- Modal confirmar pagamento dos números -->
    <b-modal
      id="confirmation-paid-modal"
      ok-variant="danger"
      ok-title="Sim, confirmo o pagamento"
      cancel-title="Não"
      @ok="handlePaidCustomerNumbers"
    >
      <template #modal-title> Confirmar pagamento </template>
      <div>
        <p>
          Ao clicar no botão sim, você confirma que o cliente realizou o
          pagamento de forma manual e fez o envio do comprovante.
        </p>
        <p>
          Após a confirmação, o cliente receberá a notificação via WhatsApp
          sobre a aprovação do pagamento.
        </p>
        <p><strong>Deseja confirmar o pagamento?</strong></p>
      </div>
    </b-modal>

    <!-- Modal confirmar liberação de números -->
    <b-modal
      id="confirmation-free-modal"
      ok-variant="danger"
      ok-title="Sim, desejo liberar os números"
      cancel-title="Não"
      @ok="handleFreeNumbers"
    >
      <template #modal-title> Liberar números reservados </template>
      <div>
        <p>
          Ao clicar no botão sim, todos os números marcados como reservado serão
          disponibilizados novamente para reserva dos clientes.
        </p>
        <p><strong>Deseja liberar todos os números reservados?</strong></p>
      </div>
    </b-modal>
  </b-container>
</template>

<script>
import LoaderVue from "@/components/_commons/Loader.vue";
import ContestNumberVue from "@/components/Contests/ContestNumber.vue";
import ContestPercentageForm from "@/components/Contests/Admin/ContestPercentageForm.vue";
import UsersSelectVue from "@/components/Users/UsersSelect.vue";

import { getContest, editContest } from "@/services/contests";
import {
  adminFreeNumbers,
  adminPaidNumbers,
  listNumbers,
} from "@/services/numbers";

export default {
  name: "AdminContestManage",
  components: {
    "my-loader": LoaderVue,
    "contest-number": ContestNumberVue,
    "contest-percentage-form": ContestPercentageForm,
    "users-select": UsersSelectVue,
  },
  data() {
    return {
      loading: false,
      contestId: null,
      customerId: null,
      numbers: [],
      selectedNumbers: [],
      filteredNumbers: [],
      quantity: 0,
      partial: 300,
      current: 0,
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
    async handleFilterByCustomer(customerId) {
      this.loading = true;
      this.customerId = customerId;

      const result = await listNumbers(this.contestId, {
        customer_id: customerId,
      });

      this.filter = "ALL";
      this.current = 0;
      this.numbers = result;
      this.filteredNumbers = [...this.numbers].splice(0, this.partial);

      this.loading = false;
    },
    async handlePaidCustomerNumbers() {
      try {
        this.loading = true;

        const result = await adminPaidNumbers(this.contestId, {
          customer_id: this.customerId,
        });

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
        await this.getContestData(this.contestId);
      }
    },
    async handleFreeNumbers() {
      try {
        this.loading = true;

        const result = await adminFreeNumbers(this.contestId);

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
        await this.getContestData(this.contestId);
      }
    },
    handleClearFilterByCustomer() {
      this.getContestData(this.contestId);
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
      this.current = 0;

      if (filter === "ALL") {
        this.filteredNumbers = [...this.numbers].splice(0, this.partial);
      } else {
        const filtered = this.numbers.filter((n) => n.status === filter);

        this.filteredNumbers = [...filtered].splice(0, this.partial);
      }
    },
    countNumbersByStatus(status) {
      const filtered = this.numbers.filter((n) => n.status === status);

      return filtered.length;
    },
    isSelected(number) {
      return !!this.selectedNumbers.find((n) => n.number === number.number);
    },
    handleScroll(el) {
      if (
        el.srcElement.offsetHeight + el.srcElement.scrollTop >=
          el.srcElement.scrollHeight &&
        this.current < this.quantity - this.partial
      ) {
        const filtered =
          this.filter === "ALL"
            ? [...this.numbers]
            : [...this.numbers].filter((n) => n.status === this.filter);

        this.current += this.partial;

        this.filteredNumbers = [...filtered].splice(this.current, this.partial);
      }
    },
  },
};
</script>

<style>
</style>