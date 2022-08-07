<template>
  <div>
    <a-page-header title="Vendedores" />

    <a-row>
      <a-col
        :xs="24"
        :md="{ span: 12, offset: 12 }"
        :lg="{ span: 8, offset: 16 }"
      >
        <a-input-search
          v-model:value="filters.search"
          placeholder="Pesquisar vendedores por nome ou WhatsApp"
          enter-button
          @input="handleSearch"
        />
      </a-col>
    </a-row>

    <a-table
      :columns="columns"
      :row-key="(record) => record.id"
      :data-source="sellers"
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

        <template v-if="column.key === 'seller_approved'">
          <a-tag v-if="record.seller_approved" color="success">
            <template #icon>
              <check-outlined />
            </template>
            Aprovado
          </a-tag>
          <a-tag v-else color="warning">
            <template #icon>
              <exclamation-circle-outlined />
            </template>
            Pendente
          </a-tag>
        </template>

        <template v-if="column.key === 'blocked'">
          <a-tag v-if="record.blocked" color="error">
            <template #icon>
              <stop-outlined />
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

        <template v-if="column.key === 'actions'">
          <a-space>
            <a-button
              type="primary"
              :disabled="record.seller_approved"
              @click="toggleApproveModal(record)"
            >
              <template #icon><check-outlined /></template>
              Aprovar
            </a-button>

            <a-button
              type="primary"
              :danger="!record.blocked"
              @click="toggleBlockedModal(record)"
            >
              <template #icon>
                <check-outlined v-if="record.blocked" />
                <stop-outlined v-else />
              </template>
              {{ record.blocked ? "Desbloquear" : "Bloquear" }}
            </a-button>
          </a-space>
        </template>
      </template>
    </a-table>

    <pagination :pager="pager" @paginate="handlePaginate" />

    <approve-seller-modal
      :seller="selectedSeller"
      :visible="showApproveModal"
      :loading="loading"
      @approve="handleApproveSeller"
      @cancel="toggleApproveModal"
    />

    <toggle-seller-modal
      :seller="selectedSeller"
      :visible="showBlockedModal"
      :loading="loading"
      @approve="handleToggleSeller"
      @cancel="toggleBlockedModal"
    />
  </div>
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
  ExclamationCircleOutlined,
  StopOutlined,
} from "@ant-design/icons-vue";

import Pagination from "@/components/_commons/Pagination.vue";
import ApproveSellerModal from "@/components/Sellers/ApproveSellerModal.vue";

import { ApiResponse, BaseQuery, PaginatedResponse } from "@/types/api.types";
import { SellerItem } from "@/types/Seller.types";
import { approveSeller, listSellers, toggleSeller } from "@/services/sellers";

import { cellphoneMask } from "@/utils/cellphoneMask";
import { notification } from "ant-design-vue";
import ToggleSellerModal from "@/components/Sellers/ToggleSellerModal.vue";
import { getErrorMessage } from "@/utils/handleError";

const columns: ColumnsType<SellerItem> = [
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
    title: "Aprovado",
    dataIndex: "seller_approved",
    key: "seller_approved",
    width: 150,
  },
  {
    title: "Data de cadastro",
    dataIndex: "created_at",
    key: "created_at",
    width: 150,
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
  name: "AdminSellerList",
  components: {
    WhatsAppOutlined,
    CloseOutlined,
    CheckOutlined,
    ExclamationCircleOutlined,
    StopOutlined,
    Pagination,
    ApproveSellerModal,
    ToggleSellerModal,
  },
  setup() {
    const loading = ref<boolean>(false);
    const sellers = ref<SellerItem[]>();
    const selectedSeller = ref<SellerItem>();
    const showApproveModal = ref<boolean>(false);
    const showBlockedModal = ref<boolean>(false);

    const filters = reactive<BaseQuery>({
      search: "",
      page: 1,
    });

    const pager = ref<PaginatedResponse<SellerItem>>({
      current_page: 1,
      total: 1,
      per_page: 1,
    });

    async function getSellers(params: BaseQuery) {
      loading.value = true;

      const response = await listSellers(params);

      pager.value.current_page = response.current_page || 1;
      pager.value.total = response.total || 1;
      pager.value.per_page = response.per_page || 1;
      sellers.value = response.data;
      loading.value = false;
    }

    const handleSearch = debounce(async (e: ChangeEvent) => {
      filters.search = e.target.value;

      await getSellers(filters);
    }, 500);

    async function handlePaginate(page: number) {
      filters.page = page;

      await getSellers(filters);
    }

    function formatDate(date: string) {
      return moment(date).format("DD/MM/YYYY");
    }

    function maskCellphone(phone: string) {
      return cellphoneMask(phone);
    }

    function whatsappLink(customer: SellerItem) {
      const { whatsapp, name } = customer;
      const message = `Olá ${name}, somos a equipe da Rifandos, tudo bem?`;

      return `https://api.whatsapp.com/send?phone=55${whatsapp}&text=${encodeURI(
        message
      )}`;
    }

    async function handleApproveSeller(seller?: SellerItem) {
      if (seller === undefined) return;

      try {
        loading.value = false;
        toggleApproveModal(undefined);

        const result = await approveSeller(seller?.id);

        notification.success({
          message: result.message,
        });

        await getSellers(filters);
      } catch (error: unknown) {
        notification.error({
          message: getErrorMessage(error),
        });
      } finally {
        loading.value = false;
      }
    }

    function toggleApproveModal(seller?: SellerItem) {
      selectedSeller.value = seller;
      showApproveModal.value = !showApproveModal.value;
    }

    async function handleToggleSeller(seller?: SellerItem) {
      if (seller === undefined) return;

      try {
        loading.value = false;
        toggleBlockedModal(undefined);

        const result: ApiResponse = await toggleSeller(seller?.id);

        notification.success({
          message: result.message,
        });

        await getSellers(filters);
      } catch (error: unknown) {
        notification.error({
          message: getErrorMessage(error),
        });
      } finally {
        loading.value = false;
      }
    }

    function toggleBlockedModal(seller?: SellerItem) {
      selectedSeller.value = seller;
      showBlockedModal.value = !showBlockedModal.value;
    }

    onMounted(async () => {
      await getSellers(filters);
    });

    return {
      filters,
      sellers,
      columns,
      loading,
      pager,
      selectedSeller,
      showApproveModal,
      showBlockedModal,

      handleSearch,
      handlePaginate,
      formatDate,
      maskCellphone,
      whatsappLink,
      toggleApproveModal,
      toggleBlockedModal,
      handleApproveSeller,
      handleToggleSeller,
    };
  },
});
</script>

<style>
</style>