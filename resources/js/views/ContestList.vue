<template>
  <container>
    <a-skeleton-input
      v-if="loading"
      size="large"
      active
      block
      style="height: 40px; width: 100%"
    />
    <h1 v-else>
      {{ seller && seller.name }} - {{ seller && seller.username }}
    </h1>

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
      <pagination :pager="pager" />
    </a-spin>
  </container>
</template>

<script>
import { computed, defineComponent, onMounted, reactive, ref } from "vue";
import { notification } from "ant-design-vue";
import { useRoute } from "vue-router";

import Container from "@/components/_commons/Container.vue";
import Pagination from "@/components/_commons/Pagination.vue";
import ContestCard from "@/components/Contests/ContestCard.vue";

import { listContestsBySeller } from "@/services/contests";

export default defineComponent({
  components: { Container, ContestCard, Pagination },
  name: "ContestList",
  setup() {
    const loading = ref(false);
    const contests = ref([]);
    const seller = ref(null);

    const params = reactive({
      limit: 12,
    });

    const pager = reactive({
      current_page: 1,
      total: 1,
    });

    const route = useRoute();
    const { username } = route.params;

    async function getSellerContests() {
      try {
        loading.value = true;

        const result = await listContestsBySeller(username, params);

        contests.value = result.data;
        seller.value = result.data[0].seller;
        pager.current_page = result.current_page;
        pager.total = result.total;
      } catch (error) {
        notification.warning({
          message: error.message,
        });
      } finally {
        loading.value = false;
      }
    }

    onMounted(async () => {
      await getSellerContests();
    });

    return {
      loading,
      contests,
      pager,
      seller,
    };
  },
});
</script>

<style>
</style>