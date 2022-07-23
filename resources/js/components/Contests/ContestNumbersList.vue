<template>
  <div style="margin: 1rem 0">
    <a-row :gutter="[16, 16]" justify="space-between">
      <a-col :xs="24" :md="12">
        <a-radio-group
          size="large"
          v-model:value="filter"
          @change="handleChange"
        >
          <a-radio-button value="ALL">Todos</a-radio-button>
          <a-radio-button value="FREE">Disponível</a-radio-button>
          <a-radio-button value="RESERVED">Reservado</a-radio-button>
          <a-radio-button value="PAID">Pago</a-radio-button>
        </a-radio-group>
      </a-col>
      <a-col :xs="24" :md="6">
        <slot name="extra"></slot>
      </a-col>
    </a-row>

    <a-divider />

    <div class="scrolling-component" ref="scrollComponent">
      <div
        v-for="number in partial"
        :key="number.number"
        class="scrolling-item"
      >
        {{ number.number }}
      </div>
    </div>
    <!-- <a-row :gutter="[16, 16]">
      <a-col
        v-for="number in numbers"
        :key="number.number"
        :xs="6"
        :sm="4"
        :lg="2"
      >
        <a-tag>
          {{ number.number }}
        </a-tag>
      </a-col>
    </a-row> -->
  </div>
</template>

<script>
import { defineComponent, onMounted, onUnmounted, ref, toRefs } from "vue";

export default defineComponent({
  name: "ContestNumbersList",
  props: {
    numbers: Array,
    filter: String,
  },
  emits: ["onSelect", "onRemove", "onFilter"],
  setup(props, { emit }) {
    const { numbers, filter } = toRefs(props);
    const split = ref(10);
    const partial = ref(numbers.value.splice(0, split.value));
    const 
    const scrollComponent = ref(null);

    console.log("ContestList: ", numbers, filter);

    // isSelected: Function,
    // onSelect: Function,
    // onRemove: Function,
    // onFilter: Function,

    function handleChange(e) {
      emit("onFilter", e.target.value);
    }

    function handleScroll(e) {
      let element = scrollComponent.value;
      if (element.getBoundingClientRect().bottom < window.innerHeight) {
        split.value += 10;
        partial.value = numbers.value.splice(0, split.value)
      }
    }

    onMounted(() => {
      window.addEventListener("scroll", handleScroll);
    });

    onUnmounted(() => {
      window.removeEventListener("scroll", handleScroll);
    });

    return {
      numbers,
      partial,
      scrollComponent,
      filter,
      handleChange,
    };
  },
});
</script>

<style>
.ant-tag {
  width: 100%;
  text-align: center;
  padding: 12px 7px;
}

.scrolling-component {
  height: 250px;
  max-height: 250px;
  overflow-y: auto;
}

.scrolling-item {
  padding: 0 16px;
  margin-bottom: 1rem;
}
</style>