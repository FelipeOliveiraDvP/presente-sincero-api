<template>
  <a-form-item
    name="bank_accounts"
    :rules="{
      required: true,
      message: 'Selecione pelo menos uma conta para receber',
    }"
  >
    <a-alert
      v-if="options && options.length === 0"
      type="warning"
      message="Você ainda não cadastrou nenhuma conta"
    >
      <template #description>
        <a-button type="primary" ghost @click="toggleAccountModal"
          >Clique aqui para criar uma nova conta</a-button
        >
      </template>
    </a-alert>
    <a-select
      v-else
      v-model:value="selectedAccounts"
      :options="options"
      :disabled="loading"
      mode="multiple"
      size="large"
      placeholder="Selecione as contas para receber"
      style="width: 100%; margin-bottom: 1rem"
      @change="handleSelectAccount"
    ></a-select>
  </a-form-item>

  <bank-account-modal
    :visible="showAccountsModal"
    @finish="handleCreateAccount"
    @cancel="toggleAccountModal"
  />
</template>

<script lang="ts">
import { listBankAccounts } from "@/services/bankAccounts";
import {
  defineComponent,
  onMounted,
  PropType,
  ref,
  toRaw,
  toRefs,
  watch,
} from "@vue/runtime-core";
import { Option } from "@/types/components.types";
import BankAccountModal from "@/components/BankAccount/BankAccountModal.vue";

export default defineComponent({
  components: { BankAccountModal },
  name: "ContestBankAccountForm",
  props: {
    bankAccounts: {
      type: Array as PropType<Array<number>>,
    },
  },
  emits: ["select"],
  setup(props, { emit }) {
    const { bankAccounts } = toRefs(props);
    const loading = ref<boolean>(false);
    const showAccountsModal = ref<boolean>(false);
    const selectedAccounts = ref<Array<number>>([]);
    const options = ref<Array<Option>>();

    async function getBankAccounts() {
      loading.value = true;

      const result = await listBankAccounts({});

      options.value = result.data
        ? result.data.map((acc) => ({
            value: acc.id,
            label: acc.name,
            isSelectOption: acc.main,
          }))
        : [];

      loading.value = false;
    }

    async function handleCreateAccount() {
      toggleAccountModal();
      await getBankAccounts();
    }

    function toggleAccountModal() {
      showAccountsModal.value = !showAccountsModal.value;
    }

    function handleSelectAccount(accounts: Option[]) {
      emit("select", accounts);
    }

    watch(bankAccounts, (newVal) => {
      selectedAccounts.value = toRaw(newVal);
    });

    onMounted(async () => {
      await getBankAccounts();
    });

    return {
      loading,
      options,
      selectedAccounts,
      showAccountsModal,
      handleSelectAccount,
      handleCreateAccount,
      toggleAccountModal,
    };
  },
});
</script>

<style>
</style>