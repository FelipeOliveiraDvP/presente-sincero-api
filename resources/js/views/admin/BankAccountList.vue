<template>
  <div>
    <div class="d-flex justify-content-between align-items-center">
      <h2>Contas bancárias</h2>
      <b-button variant="primary" @click="showModal()"> Nova conta </b-button>
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
        <b-button variant="primary" @click="showModal(data.item)">
          <font-awesome-icon :icon="['fas', 'pen']" class="icon alt" />
        </b-button>

        <b-button variant="danger" @click="handleRemoveAccount(data.item)">
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

    <account-modal
      :account="selectedAccount"
      :onSubmit="handleSaveAccount"
      @reset="hideModal"
    />
  </div>
</template>

<script>
import BankAccountModal from "@/components/BankAccount/AccountModal.vue";
import {
  listBankAccounts,
  createBankAccount,
  editBankAccount,
  removeBankAccount,
} from "@/services/bankAccounts";

export default {
  name: "AdminBankAccountList",
  components: {
    "account-modal": BankAccountModal,
  },
  data() {
    return {
      loading: false,
      selectedAccount: null,
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
          key: "agency",
          sortable: false,
          label: "Agência",
        },
        {
          key: "dv",
          sortable: false,
          label: "Dígito verificador",
        },
        {
          key: "key",
          sortable: false,
          label: "Chave PIX",
        },
        {
          key: "actions",
          sortable: false,
          label: "Ações",
        },
      ],
      items: [],
    };
  },
  mounted() {
    this.getContestsData();
  },
  methods: {
    async getContestsData() {
      try {
        this.loading = true;

        const result = await listBankAccounts(this.params);

        this.items = result.data;
        this.pager = {
          current_page: result.current_page,
          from: result.from,
          last_page: result.last_page,
          per_page: result.per_page,
          to: result.to,
          total: result.total,
        };
      } catch (error) {
        this.items = [];
      } finally {
        this.loading = false;
      }
    },
    async handleSaveAccount(account) {
      try {
        let result;
        this.loading = true;

        if (this.selectedAccount === null) {
          result = await createBankAccount(account);
        } else {
          result = await editBankAccount(account.id, account);
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
        this.selectedAccount = null;
        this.getContestsData();

        this.$bvModal.hide("bank-account-modal");
      }
    },
    async handleRemoveAccount(account) {
      try {
        this.loading = true;

        const result = await removeBankAccount(account.id);

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
        this.selectedAccount = null;
        this.getContestsData();

        this.$bvModal.hide("bank-account-modal");
      }
    },
    async handlePaginate(page) {
      this.params.page = page;
      await this.getContestsData();
    },
    showModal(account = null) {
      if (account !== null) {
        this.selectedAccount = account;
      }

      this.$bvModal.show("bank-account-modal");
    },
    hideModal() {
      this.selectedAccount = null;

      this.$bvModal.hide("bank-account-modal");
    },
  },
};
</script>

<style>
</style>