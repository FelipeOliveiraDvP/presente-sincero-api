<template>
  <container>
    <h1>Sorteios do vendedor "{{ username }}"</h1>

    <a-spin :spinning="loading" style="min-height: 400px">
      <a-row :gutter="[16, 16]">
        <a-col
          v-for="contest in contests"
          :key="contest.id"
          :xs="24"
          :lg="12"
          :xl="8"
        >
          <contest-card :contest="contest" />
        </a-col>
      </a-row>
    </a-spin>
  </container>
</template>

<script lang="ts">
import { defineComponent, onMounted, ref } from "vue";

import Container from "@/components/_commons/Container.vue";
import ContestCard from "@/components/Contests/ContestCard.vue";

import { ContestItem } from "@/types/Contest.types";
import { useRoute } from "vue-router";
import { listContestsBySeller } from "@/services/contests";
import { notification } from "ant-design-vue";
import { getErrorMessage } from "@/utils/handleError";

export default defineComponent({
  name: "ContestList",
  components: { Container, ContestCard },
  setup() {
    const loading = ref<boolean>(false);
    const contests = ref<ContestItem[]>();
    const route = useRoute();
    const { username } = route.params;

    async function getContests() {
      try {
        loading.value = true;
        const result = await listContestsBySeller(username as string, {});

        contests.value = result.data;
      } catch (error) {
        notification.error({
          message: getErrorMessage(error),
        });
      } finally {
        loading.value = false;
      }
    }

    onMounted(async () => {
      await getContests();
    });

    return {
      loading,
      username,
      contests,
    };
  },
});
</script>
