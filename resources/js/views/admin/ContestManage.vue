<template>
  <h1>Gerenciar sorteio</h1>

  <a-row :gutter="[32, 32]">
    <a-col :xs="24" :md="12">
      <finish-contest-form />
    </a-col>
    <a-col :xs="24" :md="12">
      <contest-percentage-form :contestId="contestId" :contest="contest" />
    </a-col>
  </a-row>
</template>

<script lang="ts">
import ContestPercentageForm from "@/components/Contests/Admin/ContestPercentageForm.vue";
import FinishContestForm from "@/components/Contests/Admin/FinishContestForm.vue";
import { getContest } from "@/services/contests";
import { ContestDetail } from "@/types/Contest.types";
import { getErrorMessage } from "@/utils/handleError";
import { defineComponent, onMounted, ref } from "@vue/runtime-core";
import { notification } from "ant-design-vue";
import { useRoute } from "vue-router";

export default defineComponent({
  components: { FinishContestForm, ContestPercentageForm },
  name: "AdminContestManage",
  setup() {
    const route = useRoute();
    const { id } = route.params;
    const contest = ref<ContestDetail>();

    async function getContestDetails() {
      try {
        contest.value = await getContest(Number(id));
      } catch (error) {
        notification.error({
          message: getErrorMessage(error),
        });
      }
    }

    onMounted(async () => {
      await getContestDetails();
    });

    return {
      contestId: Number(id),
      contest,
    };
  },
});
</script>

<style>
</style>