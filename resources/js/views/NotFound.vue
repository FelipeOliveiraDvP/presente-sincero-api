<template>
  <a-result
    status="404"
    title="Ops! Página não encontrada"
    sub-title="Desculpe, a página que você tentou acessar não está mais disponível."
  >
    <template #extra>
      <router-link :to="authenticated ? '/admin/sorteios' : '/'">
        <a-button type="primary">Voltar para a home</a-button>
      </router-link>
    </template>
  </a-result>
</template>

<script lang="ts">
import { useAuthStore } from "@/store/auth";
import { defineComponent } from "@vue/runtime-core";
import { storeToRefs } from "pinia";

export default defineComponent({
  name: "NotFoundPage",
  setup() {
    const store = useAuthStore();
    const { authenticated } = storeToRefs(store);

    return {
      authenticated,
    };
  },
});
</script>