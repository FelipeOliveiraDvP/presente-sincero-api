<template>
  <a-page-header title="Gerenciar sorteio" />

  <a-spin :spinning="loading" size="large">
    <a-row :gutter="[16, 16]">
      <a-col :xs="24" :md="12" :lg="6">
        <a-card>
          <a-statistic
            title="Todos os números"
            :value="numbersStatus.total"
            :value-style="{ fontSize: '3rem' }"
            style="text-align: center"
          >
          </a-statistic>
        </a-card>
      </a-col>
      <a-col :xs="24" :md="12" :lg="6">
        <a-card>
          <a-statistic
            title="Disponível"
            :value="numbersStatus.free"
            :value-style="{ color: '#22bb33', fontSize: '3rem' }"
            style="text-align: center"
          >
          </a-statistic>
        </a-card>
      </a-col>
      <a-col :xs="24" :md="12" :lg="6">
        <a-card>
          <a-statistic
            title="Reservado"
            :value="numbersStatus.reserved"
            :value-style="{ color: '#f0ad4e', fontSize: '3rem' }"
            style="text-align: center"
          >
          </a-statistic>
        </a-card>
      </a-col>
      <a-col :xs="24" :md="12" :lg="6">
        <a-card>
          <a-statistic
            title="Pago"
            :value="numbersStatus.paid"
            :value-style="{ color: '#bb2124', fontSize: '3rem' }"
            style="text-align: center"
          >
          </a-statistic>
        </a-card>
      </a-col>
    </a-row>

    <a-divider />

    <a-row :gutter="[32, 32]">
      <a-col :xs="24" :md="12">
        <contest-percentage-form :contestId="contestId" :contest="contest" />
      </a-col>
      <a-col :xs="24" :md="12">
        <finish-contest-form />
      </a-col>
    </a-row>
  </a-spin>
</template>

<script lang="ts">
import ContestPercentageForm from "@/components/Contests/Admin/ContestPercentageForm.vue";
import FinishContestForm from "@/components/Contests/Admin/FinishContestForm.vue";
import { getContest } from "@/services/contests";
import { getContestNumbersStatus } from "@/services/numbers";
import { ContestDetail } from "@/types/Contest.types";
import { NumberStatusResponse } from "@/types/Number.types";
import { getErrorMessage } from "@/utils/handleError";
import { defineComponent, onMounted, reactive, ref } from "@vue/runtime-core";
import { notification } from "ant-design-vue";
import { useRoute } from "vue-router";

export default defineComponent({
  components: { FinishContestForm, ContestPercentageForm },
  name: "AdminContestManage",
  setup() {
    const route = useRoute();
    const { id } = route.params;
    const contest = ref<ContestDetail>();
    const loading = ref<boolean>(false);

    const numbersStatus = reactive<NumberStatusResponse>({
      total: 0,
      free: 0,
      reserved: 0,
      paid: 0,
    });

    async function getContestDetails() {
      try {
        loading.value = true;
        contest.value = await getContest(Number(id));
      } catch (error) {
        notification.error({
          message: getErrorMessage(error),
        });
      } finally {
        loading.value = false;
      }
    }

    async function getNumbersStatus() {
      try {
        loading.value = true;

        const result = await getContestNumbersStatus(Number(id));

        numbersStatus.total = result.total;
        numbersStatus.free = result.free;
        numbersStatus.reserved = result.reserved;
        numbersStatus.paid = result.paid;
      } catch (error) {
        notification.error({
          message: getErrorMessage(error),
        });
      } finally {
        loading.value = false;
      }
    }

    onMounted(async () => {
      await getContestDetails();
      await getNumbersStatus();
    });

    return {
      contestId: Number(id),
      contest,
      numbersStatus,
      loading,
    };
  },
});
</script>

<style>
</style>