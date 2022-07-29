<template>
  <a-page-header title="Minhas contas bancárias">
    <template #extra>
      <a-button type="primary">Nova conta</a-button>
    </template>
  </a-page-header>

  <a-row>
    <a-col
      :xs="24"
      :md="{ span: 12, offset: 12 }"
      :lg="{ span: 6, offset: 18 }"
    >
      <a-input-search
        v-model:value="filters.search"
        placeholder="Pesquisar contas"
        enter-button
        @input="handleSearch"
      />
    </a-col>
  </a-row>

  <a-table
    :columns="columns"
    :row-key="(record) => record.id"
    :data-source="accounts"
    :scroll="{ x: 950 }"
    :loading="loading"
    :pagination="false"
  >
    <template #bodyCell="{ column, record }">
      <template v-if="column.key === 'type'">
        {{ formatAccountType(record.type) }}
      </template>

      <template v-if="column.key === 'main'">
        <a-tag v-if="record.main" color="green">Sim</a-tag>
        <a-tag v-else color="red">Não</a-tag>
      </template>

      <template v-if="column.key === 'actions'">
        <a-space>
          <a-button type="primary">
            <template #icon><form-outlined /></template>
            Editar
          </a-button>

          <a-button type="primary" danger>
            <template #icon><delete-outlined /></template>
            Excluir
          </a-button>
        </a-space>
      </template>
    </template>
  </a-table>

  <pagination :pager="pager" @paginate="handlePaginate" />

  <a-divider />

  <a-typography-title :level="3" class="section-title">
    Mercado Pago
    <img src="/img/mercado-pago.png" />
  </a-typography-title>

  <a-typography-paragraph>
    Adicione o seu token do Mercado Pago para receber o pagamento dos números do
    sorteio. Para conseguir o token, você vai precisar de uma conta verificada e
    aprovada no Mercado Pago. <br />
    Na sua conta, você deve acessar
    <a
      href="https://www.mercadopago.com.br/settings/account/credentials"
      target="_blank"
      >Seu negócio > Configurações > Credenciais > Credenciais de produção</a
    >
    e copiar o "Access Token". Copie e cole o código no campo abaixo, e depois
    clique em salvar.
  </a-typography-paragraph>

  <mercado-pago-form :loading="loading" @onFinish="handleSaveMpToken" />
</template>

<script lang="ts">
import { defineComponent, onMounted, reactive, ref } from "@vue/runtime-core";
import { ColumnsType } from "ant-design-vue/lib/vc-table/interface";
import { ChangeEvent } from "ant-design-vue/lib/_util/EventInterface";
import { debounce } from "lodash";
import { FormOutlined, DeleteOutlined } from "@ant-design/icons-vue";

import { listBankAccounts, saveMPAccessToken } from "@/services/bankAccounts";
import {
  ApiResponse,
  BaseQuery,
  ErrorResponse,
  PaginatedResponse,
} from "@/types/api.types";
import { BankAccountItem, UpdateMercadoToken } from "@/types/BankAccount.types";
import Pagination from "@/components/_commons/Pagination.vue";
import MercadoPagoForm from "@/components/BankAccount/MercadoPagoForm.vue";
import { notification } from "ant-design-vue";

const columns: ColumnsType<BankAccountItem> = [
  {
    title: "Nome",
    dataIndex: "name",
    key: "name",
    width: 200,
  },
  {
    title: "Tipo de conta",
    dataIndex: "type",
    key: "type",
    width: 200,
  },
  {
    title: "Principal",
    dataIndex: "main",
    key: "main",
    width: 100,
  },
  {
    title: "N° da conta",
    dataIndex: "cc",
    key: "cc",
    width: 150,
  },
  {
    title: "Agencia",
    dataIndex: "agency",
    key: "agency",
    width: 150,
  },
  {
    title: "DV",
    dataIndex: "dv",
    key: "dv",
    width: 100,
  },
  {
    title: "Chave PIX",
    dataIndex: "key",
    key: "key",
    width: 300,
  },
  {
    title: "Ações",
    dataIndex: "actions",
    key: "actions",
    align: "right",
    width: 100,
  },
];

export default defineComponent({
  name: "AdminBankAccountList",
  components: {
    FormOutlined,
    DeleteOutlined,
    Pagination,
    MercadoPagoForm,
  },
  setup() {
    const loading = ref<boolean>(false);
    const accounts = ref<BankAccountItem[]>();
    const pager = ref<PaginatedResponse<BankAccountItem>>({
      current_page: 1,
      total: 1,
      per_page: 1,
    });

    const filters = reactive<BaseQuery>({
      search: "",
      page: 1,
    });

    const handleSearch = debounce(async (e: ChangeEvent) => {
      filters.search = e.target.value;

      await getAccounts(filters);
    }, 500);

    async function getAccounts(params: BaseQuery) {
      loading.value = true;
      const response: PaginatedResponse<BankAccountItem> =
        await listBankAccounts(params);

      pager.value.current_page = response.current_page || 1;
      pager.value.total = response.total || 1;
      pager.value.per_page = response.per_page || 1;

      accounts.value = response.data;
      loading.value = false;
    }

    async function handlePaginate(page: number) {
      filters.page = page;

      await getAccounts(filters);
    }

    async function handleSaveMpToken(values: UpdateMercadoToken) {
      try {
        loading.value = true;

        const result: ApiResponse = await saveMPAccessToken(values);

        notification.success({
          message: result.message,
        });
      } catch (error: unknown) {
        const { message } = error as ErrorResponse;

        notification.error({
          message: message,
        });
      } finally {
        loading.value = false;
      }
    }

    function formatAccountType(type: string) {
      const accountTypes = {
        BANK: "Conta bancária",
        PIX: "PIX",
      };

      return accountTypes[type];
    }

    onMounted(async () => {
      await getAccounts(filters);
    });

    return {
      columns,
      loading,
      accounts,
      pager,
      filters,
      handleSearch,
      handlePaginate,
      formatAccountType,
      handleSaveMpToken,
    };
  },
});
</script>

<style>
.section-title img {
  max-width: 100px;
  margin-left: 5px;
}
</style>