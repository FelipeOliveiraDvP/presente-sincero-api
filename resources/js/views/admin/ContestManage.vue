<template>
  <h1>Gerenciar sorteio</h1>

  <finish-contest-form />
</template>

<script lang="ts">
import FinishContestForm from "@/components/Contests/Admin/FinishContestForm.vue";
import { defineComponent } from "@vue/runtime-core";

export default defineComponent({
  components: { FinishContestForm },
  name: "AdminContestManage",
});
</script>

<style>
</style>