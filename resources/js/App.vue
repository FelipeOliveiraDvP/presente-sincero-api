<template>
  <a-config-provider :locale="ptBR">
    <div class="site">
      <router-view />
    </div>
  </a-config-provider>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import ptBR from "ant-design-vue/es/locale/pt_BR";

export default defineComponent({
  name: "App",
  setup() {
    return {
      ptBR,
    };
  },
});
</script>

<style>
.ant-layout-content {
  min-height: unset !important;
  height: auto;
}
.ant-input-number {
  width: 100%;
}
.ant-btn.ant-btn-primary {
  background: #44853c;
  border-color: #44853c;
}
.ant-btn.ant-btn-primary:hover {
  background: #5f8f59;
  border-color: #5f8f59;
}
</style>