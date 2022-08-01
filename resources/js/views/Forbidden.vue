<template>
  <a-result
    status="403"
    title="Ops! Acesso proibido"
    sub-title="Desculpe, você não tem permissão para acessar essa página."
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
  name: "ForbiddenPage",
  setup() {
    const store = useAuthStore();
    const { authenticated } = storeToRefs(store);

    return {
      authenticated,
    };
  },
});
</script>