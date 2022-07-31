<template>
  <a-page-header title="Meus sorteios">
    <template #extra>
      <router-link to="/admin/sorteios/novo-sorteio">
        <a-button type="primary">Novo sorteio</a-button>
      </router-link>
    </template>
  </a-page-header>

  <a-row>
    <a-col
      :xs="24"
      :md="{ span: 12, offset: 12 }"
      :lg="{ span: 6, offset: 18 }"
    >
      <a-input-search
        v-model:value="filters.title"
        placeholder="Pesquisar sorteios"
        enter-button
        @input="handleSearch"
      />
    </a-col>
  </a-row>

  <a-table
    :columns="columns"
    :row-key="(record) => record.id"
    :data-source="contests"
    :scroll="{ x: 950 }"
    :loading="loading"
    :pagination="false"
  >
    <template #bodyCell="{ column, record }">
      <template v-if="column.key === 'image'">
        <a-image :preview="false" :src="record.gallery[0].path" />
      </template>

      <template v-if="column.key === 'start_date'">
        {{ formatDate(record.start_date) }}
      </template>

      <template v-if="column.key === 'price'">
        {{ formatPrice(record.price) }}
      </template>

      <template v-if="column.key === 'paid_percentage'">
        <a-progress :percent="record.paid_percentage" />
      </template>

      <template v-if="column.key === 'actions'">
        <a-space direction="vertical">
          <router-link :to="`/admin/sorteios/${record.id}`">
            <a-button type="primary" :block="true">
              <template #icon><form-outlined /></template>
              Editar
            </a-button>
          </router-link>

          <router-link :to="`/admin/sorteios/${record.id}/gerenciar`">
            <a-button type="primary" block>
              <template #icon><setting-outlined /></template>
              Gerenciar
            </a-button>
          </router-link>

          <router-link :to="`/admin/sorteios/${record.id}/pedidos`">
            <a-button type="primary" block>
              <template #icon><dollar-outlined /></template>
              Pedidos
            </a-button>
          </router-link>

          <router-link
            :to="`/${record.seller.username}/${record.slug}`"
            target="_blank"
          >
            <a-button type="primary" block>
              <template #icon><eye-outlined /></template>
              Visualizar
            </a-button>
          </router-link>
        </a-space>
      </template>
    </template>
  </a-table>

  <pagination :pager="pager" @paginate="handlePaginate" />
</template>

<script lang="ts">
import { defineComponent, onMounted, reactive, ref } from "@vue/runtime-core";
import { ChangeEvent } from "ant-design-vue/lib/_util/EventInterface";
import { ColumnsType } from "ant-design-vue/lib/table";
import { debounce } from "lodash";
import {
  FormOutlined,
  SettingOutlined,
  DollarOutlined,
  EyeOutlined,
  DownOutlined,
} from "@ant-design/icons-vue";
import * as moment from "moment";

import Pagination from "@/components/_commons/Pagination.vue";

import { ContestItem, ListContestsQuery } from "@/types/Contest.types";
import { listContestsByUser } from "@/services/contests";
import { PaginatedResponse } from "@/types/api.types";
import { moneyFormat } from "@/utils/moneyFormat";

const columns: ColumnsType<ContestItem> = [
  {
    title: "Imagem",
    dataIndex: "image",
    key: "image",
    width: 200,
    align: "center",
  },
  {
    title: "Título",
    dataIndex: "title",
    key: "title",
    width: 300,
  },
  {
    title: "Data de início",
    dataIndex: "start_date",
    key: "start_date",
    width: 150,
  },
  {
    title: "R$ Valor",
    dataIndex: "price",
    key: "price",
    width: 150,
  },
  {
    title: "Quantidade",
    dataIndex: "quantity",
    key: "quantity",
    width: 150,
  },
  {
    title: "% Vendido",
    dataIndex: "paid_percentage",
    key: "paid_percentage",
    width: 300,
  },
  {
    title: "Ações",
    dataIndex: "actions",
    key: "actions",
    align: "center",
    width: 150,
  },
];

export default defineComponent({
  name: "AdminContestList",
  components: {
    Pagination,
    FormOutlined,
    SettingOutlined,
    DollarOutlined,
    EyeOutlined,
    DownOutlined,
  },
  setup() {
    const loading = ref<boolean>(false);
    const contests = ref<ContestItem[]>();

    const filters = reactive<ListContestsQuery>({
      title: "",
      limit: 10,
      page: 1,
    });

    const pager = ref<PaginatedResponse<ContestItem>>({
      current_page: 1,
      total: 1,
      per_page: 1,
    });

    const handleSearch = debounce(async (e: ChangeEvent) => {
      filters.title = e.target.value;

      await getContests(filters);
    }, 500);

    async function getContests(params: ListContestsQuery) {
      loading.value = true;
      const response: PaginatedResponse<ContestItem> = await listContestsByUser(
        params
      );

      pager.value.current_page = response.current_page || 1;
      pager.value.total = response.total || 1;
      pager.value.per_page = response.per_page || 1;
      contests.value = response.data;
      loading.value = false;
    }

    async function handlePaginate(page: number) {
      filters.page = page;

      await getContests(filters);
    }

    function formatDate(date: string) {
      return moment(date).format("DD/MM/YYYY");
    }

    function formatPrice(value: number) {
      return moneyFormat(value);
    }

    onMounted(async () => {
      await getContests(filters);
    });

    return {
      filters,
      contests,
      columns,
      loading,
      pager,
      handleSearch,
      handlePaginate,
      formatDate,
      formatPrice,
    };
  },
});
</script>

<style>
.ant-space-item {
  width: 100%;
}
</style>