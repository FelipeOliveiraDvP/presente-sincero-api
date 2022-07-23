<template>
  <a-page-header style="border: 1px solid rgb(235, 237, 240)" title="Sorteios"
    sub-title="Escolha o sorteio ao qual deseja participar" />

  <Container>
    <a-spin :spinning="loading" style="min-height: 400px;">
      <a-row :gutter="[16, 16]">
        <a-col v-for="contest in contests" :key="contest.id" :xs="24" :sm="12" :md="8" :lg="6">
          <ContestCard :contest="contest" />
        </a-col>
      </a-row>
      <Pagination :pager="pager" />
    </a-spin>
  </Container>
</template>

<script>
import { listContestsBySeller } from "@/services/contests";
import { defineComponent } from 'vue';
import { notification } from 'ant-design-vue';
import Pagination from '@/components/_commons/Pagination.vue';
import Container from '@/components/_commons/Container.vue';
import ContestCard from '@/components/Contests/ContestCard.vue';

export default defineComponent({
  name: "ContestList",
  components: {
    Pagination,
    Container,
    ContestCard
  },
  data() {
    return {
      loading: false,
      params: {
        limit: 12,
      },
      pager: {
        current_page: 1,
        total: 1
      },
      contests: [],
    };
  },
  mounted() {
    const { username } = this.$route.params;

    this.getContests(username);
  },
  methods: {
    async getContests(username) {
      try {
        this.loading = true;

        const result = await listContestsBySeller(username, this.params);

        this.contests = result.data;
        this.pager.current_page = result.current_page;
        this.pager.total = result.total;
      } catch (error) {
        notification.warning({
          message: error.message
        });
      } finally {
        this.loading = false;
      }
    },
  },
});
</script>

<style>
</style>