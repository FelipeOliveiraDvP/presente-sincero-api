<template>
  <container>
    <h1>Bem vindo a Rifandos</h1>

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

import { listContests } from "@/services/contests";
import { ContestItem } from "@/types/Contest.types";

export default defineComponent({
  name: "Home",
  components: { Container, ContestCard },
  setup() {
    const loading = ref<boolean>(false);
    const contests = ref<ContestItem[]>();

    async function getContests() {
      loading.value = true;

      const result = await listContests({});

      contests.value = result.data;
      loading.value = false;
    }

    onMounted(async () => {
      await getContests();
    });

    return {
      loading,
      contests,
    };
  },
});
</script>
