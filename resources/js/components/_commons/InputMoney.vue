<template>
  <a-input ref="inputRef" style="width: 100%" />
</template>

<script lang="ts">
import { defineComponent } from "@vue/runtime-core";
import { useCurrencyInput } from "vue-currency-input";

export default defineComponent({
  name: "InputMoney",
  props: {
    modelValue: Number,
    options: Object,
  },
  setup(props) {
    const { inputRef } = useCurrencyInput({
      ...props,
      currency: "BRL",
      precision: 2,
      locale: "pt-BR",
      autoDecimalDigits: true,
      hideCurrencySymbolOnFocus: false,
    });

    return {
      inputRef,
    };
  },
});
</script>

<style>
</style>