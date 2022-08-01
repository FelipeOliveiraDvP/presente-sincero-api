<template>
  <div>
    <a-row>
      <a-col
        :xs="24"
        :md="12"
        :style="{
          'background-image': `url(${contest.gallery[0].path})`,
        }"
        class="slide-image"
      />

      <a-col :xs="24" :md="12" class="slide-content">
        <div>
          <h2>{{ contest.title || "Nome" }}</h2>
          <p>{{ contest.short_description }}</p>
        </div>

        <div style="margin-bottom: 1rem">
          <h4>Total vendido</h4>
          <a-progress
            title="Números vendidos"
            strokeColor="#F93154"
            :percent="41"
            :stroke-width="40"
          >
            <template #format="percent">
              <span style="color: white; font-size: 1rem">{{ percent }}%</span>
            </template>
          </a-progress>
        </div>

        <a-button type="primary" block size="large">
          Garanta seu número por {{ formatMoney(contest.price) }}
        </a-button>
      </a-col>
    </a-row>
  </div>
</template>

<script>
import { defineComponent } from "vue";
import { moneyFormat } from "@/utils/moneyFormat";

export default defineComponent({
  name: "ContestSlide",
  props: {
    contest: Object,
  },
  methods: {
    formatMoney(value) {
      return moneyFormat(value);
    },
  },
});
</script>

<style>
.slide-image {
  min-height: 500px;
  background-repeat: no-repeat;
  background-position: center;
  background-size: cover;
}

.slide-content {
  background-color: #262626;
  color: #fbfbfb;
  padding: 1rem;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.slide-content h2,
.slide-content h4 {
  color: inherit;
}

.slide-content h2 {
  font-size: 2rem;
}

.slide-content p {
  font-size: 1.4rem;
}

.slide-content .ant-progress-bg {
  border-radius: 0;
}

.slide-content .ant-progress-inner {
  border: 1px solid #fff;
  background-color: #262626;
  border-radius: 8px;
}
</style>