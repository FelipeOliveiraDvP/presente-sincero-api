<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2>Contas bancárias</h2>
      <b-button variant="primary" @click="showModal()"> Nova conta </b-button>
    </div>

    <div class="table-responsive">
      <b-table
        id="accounts-list"
        class="text-white"
        :fields="fields"
        :items="items"
        :busy.sync="loading"
      >
        <template v-slot:head()="scope">
          <div style="width: 150px">{{ scope.label }}</div>
        </template>

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
    </div>

    <b-pagination
      :v-model="params.page"
      :per-page="pager.per_page"
      :total-rows="pager.total"
      pills
      align="end"
      @change="handlePaginate"
    ></b-pagination>

    <div class="my-4 row">
      <div class="col-12 col-lg-8">
        <h3>Mercado Pago</h3>
        <p>
          Adicione o seu token do Mercado Pago para receber o pagamento dos
          números do sorteio. Para conseguir o token, você vai precisar de uma
          conta verificada e aprovada no Mercado Pago. Na sua conta, você deve
          acessar
          <a
            href="https://www.mercadopago.com.br/settings/account/credentials"
            target="_blank"
            rel="noopener noreferrer"
          >
            Seu negócio > Configurações > Credenciais > Credenciais de produção.
          </a>
          e copiar o "Access Token".
        </p>
      </div>
      <div class="col-12">
        <b-form @submit.stop.prevent="onSaveMPToken">
          <b-form-input
            id="mpAccessToken"
            v-model="mpAccessToken"
          ></b-form-input>
          <b-button
            type="submit"
            block
            variant="primary"
            class="my-4"
            :disabled="loading"
            >Salvar</b-button
          >
        </b-form>
      </div>
    </div>

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
  saveMPAccessToken,
} from "@/services/bankAccounts";
import { getProfile } from "@/services/auth";

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
      mpAccessToken: null,
    };
  },
  async mounted() {
    this.loading = true;

    const result = await getProfile();
    const { mp_access_token } = result.user;

    this.mpAccessToken = mp_access_token;
    this.getContestsData();
    this.loading = false;
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
    async onSaveMPToken() {
      this.loading = true;

      const result = await saveMPAccessToken({
        mp_access_token: this.mpAccessToken,
      });

      this.$toasted.show(result.message, {
        type: "success",
        theme: "toasted-primary",
        position: "top-right",
        duration: 3000,
      });

      this.loading = false;
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