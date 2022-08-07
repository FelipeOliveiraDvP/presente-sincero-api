<template>
  <a-card hoverable class="contest-card">
    <a-row>
      <a-col :xs="24" :md="12">
        <div class="contest-card-img">
          <img :alt="contest.title" :src="thumbnail" />
        </div>
      </a-col>
      <a-col class="contest-card-info" :xs="24" :md="12">
        <div>
          <a-typography-title :level="4">
            {{ contest && contest.title }}
          </a-typography-title>
          <a-typography-paragraph
            :ellipsis="{ rows: 4 }"
            v-model:content="contest.short_description"
          >
          </a-typography-paragraph>
        </div>

        <router-link :to="contestLink">
          <a-button type="primary" block>
            Compre agora por {{ price }}
          </a-button>
        </router-link>
      </a-col>
    </a-row>
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

<style lang="scss">
.contest-card,
.contest-card .ant-card-body {
  padding: 0;
}
.contest-card .contest-card-img {
  overflow: hidden;
}
.contest-card .contest-card-img img {
  width: 100%;
  max-width: 100%;
  height: 250px;
  object-fit: cover;
}
.contest-card .contest-card-info {
  padding: 1rem;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}
</style>

