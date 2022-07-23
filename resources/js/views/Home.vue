<template>
  <container>
    <h1>Bem vindo a Rifandos</h1>

    <a-spin :spinning="loading" style="min-height: 400px">
      <a-row :gutter="[16, 16]">
        <a-col
          v-for="contest in contests"
          :key="contest.id"
          :xs="24"
          :sm="12"
          :md="8"
          :lg="6"
        >
          <contest-card :contest="contest" />
        </a-col>
      </a-row>
    </a-spin>
  </container>
</template>

<script>
import { defineComponent, onMounted, ref } from "vue";

import Container from "@/components/_commons/Container.vue";
import ContestCard from "@/components/Contests/ContestCard.vue";

import { listContests } from "@/services/contests";

export default defineComponent({
  components: { Container, ContestCard },
  name: "Home",
  setup() {
    const loading = ref(false);
    const contests = ref([]);

    async function getContests() {
      loading.value = true;
      const result = await listContests();
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
