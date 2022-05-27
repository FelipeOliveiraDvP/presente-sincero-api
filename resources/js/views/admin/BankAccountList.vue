<template>
  <div>
    <div class="d-flex justify-content-between align-items-center">
      <h2>Contas bancárias</h2>
      <b-button variant="primary" @click="toggleModal()"> Nova conta </b-button>
    </div>

    <b-table
      id="accounts-list"
      class="text-white"
      :fields="fields"
      :items="items"
      :busy.sync="loading"
    >
      <template #cell(type)="data">
        {{ data.item.type === "PIX" ? "PIX" : "Conta bancária" }}
      </template>

      <template #cell(actions)="data">
        <b-button variant="primary" @click="toggleModal(data.item)">
          <font-awesome-icon :icon="['fas', 'pen']" class="icon alt" />
        </b-button>

        <b-button variant="danger" @click="handleRemove(data.item)">
          <font-awesome-icon :icon="['fas', 'trash']" class="icon alt" />
        </b-button>
      </template>
    </b-table>

    <b-pagination
      :v-model="params.page"
      :per-page="pager.per_page"
      :total-rows="pager.total"
      pills
      align="end"
      @change="handlePaginate"
    ></b-pagination>

    <account-modal :account="selectedAccount" :onSubmit="handleSaveAccount" />
  </div>
</template>

<script>
// import { listContests } from "../../services/contests";
import BankAccountModal from "../../components/BankAccount/AccountModal.vue";

export default {
  name: "AdminBankAccountList",
  components: {
    "account-modal": BankAccountModal,
  },
  data() {
    return {
      loading: false,
      selectedAccount: undefined,
      params: {
        limit: 10,
        page: 1,
      },
      pager: {
        current_page: 1,
        first_page: 1,
        last_page: 1,
        per_page: 10,
        from: 1,
        to: 10,
        total: 10,
      },
      fields: [
        {
          key: "type",
          sortable: true,
          label: "Tipo",
        },
        {
          key: "name",
          sortable: true,
          label: "Nome",
        },
        {
          key: "cc",
          sortable: false,
          label: "Conta corrente",
        },
        {
          key: "dv",
          sortable: false,
          label: "Dígito verificador",
        },
        {
          key: "chave",
          sortable: false,
          label: "Chave PIX",
        },
        {
          key: "actions",
          sortable: false,
          label: "Ações",
        },
      ],
      items: [
        {
          id: 1,
          type: "PIX",
          name: "Pagar com PIX",
          cc: null,
          agencia: null,
          dv: null,
          chave: "123.456.789-09",
        },
        {
          id: 2,
          type: "BANK",
          name: "Banco do Brasil",
          cc: "195866",
          agencia: "5901",
          dv: "6",
          chave: null,
        },
      ],
    };
  },
  computed: {},
  mounted() {
    this.getContests();
  },
  methods: {
    async getContests() {
      // try {
      //   this.loading = true;
      //   const result = await listContests(this.params);
      //   this.items = result.data;
      //   this.pager = {
      //     current_page: result.current_page,
      //     from: result.from,
      //     last_page: result.last_page,
      //     per_page: result.per_page,
      //     to: result.to,
      //     total: result.total,
      //   };
      // } catch (error) {
      //   this.items = [];
      // } finally {
      //   this.loading = false;
      // }
    },
    async handlePaginate(page) {
      this.params.page = page;
      await this.getContests();
    },
    toggleModal(account = undefined) {
      this.selectedAccount = account;

      this.$bvModal.show("bank-account-modal");
    },
    handleSaveAccount(account) {
      console.log(account);
    },
  },
};
</script>

<style>
</style>