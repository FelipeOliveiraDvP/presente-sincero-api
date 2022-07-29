<template>
  <a-page-header title="Clientes" />

  <a-row>
    <a-col
      :xs="24"
      :md="{ span: 12, offset: 12 }"
      :lg="{ span: 6, offset: 18 }"
    >
      <a-input-search
        v-model:value="filters.search"
        placeholder="Pesquisar clientes por nome ou WhatsApp"
        enter-button
        @input="handleSearch"
      />
    </a-col>
  </a-row>

  <a-table
    :columns="columns"
    :row-key="(record) => record.id"
    :data-source="customers"
    :scroll="{ x: 950 }"
    :loading="loading"
    :pagination="false"
  >
    <template #bodyCell="{ column, record }">
      <template v-if="column.key === 'whatsapp'">
        <a-typography-link :href="whatsappLink(record)" target="_blank">
          {{ maskCellphone(record.whatsapp) }}
          <whats-app-outlined />
        </a-typography-link>
      </template>

      <template v-if="column.key === 'blocked'">
        <a-tag v-if="record.blocked" color="error">
          <template #icon>
            <close-outlined />
          </template>
          Bloqueado
        </a-tag>
        <a-tag v-else color="success">
          <template #icon>
            <check-outlined />
          </template>
          Ativo
        </a-tag>
      </template>

      <template v-if="column.key === 'created_at'">
        {{ formatDate(record.created_at) }}
      </template>
    </template>
  </a-table>

  <pagination :pager="pager" @paginate="handlePaginate" />
</template>

<script lang="ts">
import { defineComponent, onMounted, reactive, ref } from "@vue/runtime-core";
import { ChangeEvent } from "ant-design-vue/lib/_util/EventInterface";
import { ColumnsType } from "ant-design-vue/lib/vc-table/interface";
import * as moment from "moment";
import { debounce } from "lodash";
import {
  WhatsAppOutlined,
  CloseOutlined,
  CheckOutlined,
} from "@ant-design/icons-vue";

import { CustomerItem } from "@/types/Customer.types";
import { BaseQuery, PaginatedResponse } from "@/types/api.types";
import { listCustomers } from "@/services/customers";
import { cellphoneMask } from "@/utils/cellphoneMask";
import Pagination from "@/components/_commons/Pagination.vue";

const columns: ColumnsType<CustomerItem> = [
  {
    title: "Nome",
    dataIndex: "name",
    key: "name",
    width: 200,
  },
  {
    title: "WhatsApp",
    dataIndex: "whatsapp",
    key: "whatsapp",
    width: 200,
  },
  {
    title: "Status",
    dataIndex: "blocked",
    key: "blocked",
    width: 150,
  },
  {
    title: "Data de cadastro",
    dataIndex: "created_at",
    key: "created_at",
    width: 150,
  },
];

export default defineComponent({
  name: "AdminCustomerList",
  components: {
    WhatsAppOutlined,
    CloseOutlined,
    CheckOutlined,
    Pagination,
  },
  setup() {
    const loading = ref<boolean>(false);
    const customers = ref<CustomerItem[]>();

    const filters = reactive<BaseQuery>({
      search: "",
      page: 1,
    });

    const pager = ref<PaginatedResponse<CustomerItem>>({
      current_page: 1,
      total: 1,
      per_page: 1,
    });

    async function getCustomers(params: BaseQuery) {
      loading.value = true;

      const response: PaginatedResponse<CustomerItem> = await listCustomers(
        params
      );

      pager.value.current_page = response.current_page || 1;
      pager.value.total = response.total || 1;
      pager.value.per_page = response.per_page || 1;
      customers.value = response.data;
      loading.value = false;
    }

    const handleSearch = debounce(async (e: ChangeEvent) => {
      filters.search = e.target.value;

      await getCustomers(filters);
    }, 500);

    async function handlePaginate(page: number) {
      filters.page = page;

      await getCustomers(filters);
    }

    function formatDate(date: string) {
      return moment(date).format("DD/MM/YYYY");
    }

    function maskCellphone(phone: string) {
      return cellphoneMask(phone);
    }

    function whatsappLink(customer: CustomerItem) {
      const { whatsapp, name } = customer;
      const message = `Olá ${name}, somos a equipe da Rifandos, tudo bem?`;

      return `https://api.whatsapp.com/send?phone=55${whatsapp}&text=${encodeURI(
        message
      )}`;
    }

    onMounted(async () => {
      await getCustomers(filters);
    });

    return {
      filters,
      customers,
      columns,
      loading,
      pager,
      handleSearch,
      handlePaginate,
      formatDate,
      maskCellphone,
      whatsappLink,
    };
  },
});
</script>

<style>
</style>