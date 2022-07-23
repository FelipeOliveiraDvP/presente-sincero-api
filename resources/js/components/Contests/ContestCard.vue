<template>
  <a-card hoverable style="min-height: 400px">
    <template #cover>
      <img :alt="contest.title" :src="thumbnail" />
    </template>
    <template #actions>
      <router-link :to="contestLink">
        <a-button type="primary"> Compre agora por {{ price }} </a-button>
      </router-link>
    </template>
    <a-card-meta
      :title="contest.title"
      :description="contest.short_description"
    />
  </a-card>
</template>

<script>
import { defineComponent } from "vue";
import { moneyFormat } from "@/utils/moneyFormat";

export default defineComponent({
  props: {
    contest: Object,
  },
  name: "ContestCard",
  computed: {
    thumbnail() {
      const { gallery } = this.contest;

      return gallery[0].path;
    },
    price() {
      return moneyFormat(this.contest.price);
    },
    contestLink() {
      const { username } = this.$route.params;

      return `/${username}/${this.contest.slug}`;
    },
  },
});
</script>

<style>
</style>