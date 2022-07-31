<template>
  <a-form-item name="bank_accounts">
    <a-alert
      v-if="options && options.length === 0"
      type="warning"
      message="Você ainda não cadastrou nenhuma conta"
    >
      <template #description>
        <a-button type="primary" @click="toggleAccountModal"
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
import { ContestRequest } from "@/types/Contest.types";
import {
  defineComponent,
  onMounted,
  PropType,
  ref,
  toRefs,
  watch,
} from "@vue/runtime-core";
import { Option } from "@/types/components.types";
import BankAccountModal from "@/components/BankAccount/BankAccountModal.vue";

export default defineComponent({
  components: { BankAccountModal },
  name: "ContestBankAccountForm",
  props: {
    formState: {
      type: Object as PropType<ContestRequest>,
      required: true,
    },
  },
  emits: ["select"],
  setup(props, { emit }) {
    const { formState } = toRefs(props);
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

    watch(options, (_) => {
      const { bank_accounts } = formState.value;

      if (options && bank_accounts && bank_accounts.length > 0) {
        bank_accounts.forEach((acc) => {
          const selected = options.value.find((opt) => opt.value === acc);

          if (selected) {
            selectedAccounts.value.push(selected.value);
          }
        });
      }
    });

    onMounted(async () => {
      await getBankAccounts();
    });

    return {
      loading,
      formState,
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