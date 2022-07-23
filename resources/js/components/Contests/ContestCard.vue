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
import { computed, defineComponent, toRefs } from "vue";
import { useRoute } from "vue-router";

import { moneyFormat } from "@/utils/moneyFormat";

export default defineComponent({
  name: "ContestCard",
  props: {
    contest: Object,
  },
  setup(props) {
    const route = useRoute();
    const { contest } = toRefs(props);

    const price = computed(() => moneyFormat(contest.value.price));

    const thumbnail = computed(() => {
      const { gallery } = contest.value;

      return gallery[0].path;
    });

    const contestLink = computed(() => {
      const { username } = route.params;
      const { seller, slug } = contest.value;

      return `/${username || seller.username}/${slug}`;
    });

    return {
      contest,
      price,
      thumbnail,
      contestLink,
    };
  },
});
</script>

<style>
</style>