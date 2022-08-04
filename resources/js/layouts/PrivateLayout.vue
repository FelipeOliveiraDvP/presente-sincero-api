<template>
  <a-layout style="height: 100vh">
    <a-layout-sider breakpoint="lg" collapsed-width="0">
      <div class="logo">
        <img src="/img/logo.png" alt="Rifandos" />
      </div>
      <a-menu v-model:selectedKeys="selectedKeys" theme="dark" mode="inline">
        <a-sub-menu key="/sorteios">
          <template #title>
            <span>
              <dollar-circle-outlined />
              <span>Sorteios</span>
            </span>
          </template>

          <a-menu-item key="/todos">
            <router-link to="/admin/sorteios"> Todos os sorteios </router-link>
          </a-menu-item>

          <a-menu-item key="/novo-sorteio">
            <router-link to="/admin/sorteios/novo-sorteio">
              Novo sorteio
            </router-link>
          </a-menu-item>
        </a-sub-menu>

        <a-menu-item key="/contas">
          <router-link to="/admin/contas">
            <bank-outlined />
            <span class="nav-text">Contas bancárias</span>
          </router-link>
        </a-menu-item>

        <a-menu-item v-if="isAdmin" key="/clientes">
          <router-link to="/admin/clientes">
            <team-outlined />
            <span class="nav-text">Clientes</span>
          </router-link>
        </a-menu-item>

        <a-menu-item v-if="isAdmin" key="/vendedores">
          <router-link to="/admin/vendedores">
            <shop-outlined />
            <span class="nav-text">Vendedores</span>
          </router-link>
        </a-menu-item>

        <a-menu-item key="/minha-conta">
          <router-link to="/admin/minha-conta">
            <setting-outlined />
            <span class="nav-text">Minha conta</span>
          </router-link>
        </a-menu-item>
      </a-menu>
    </a-layout-sider>
    <a-layout style="overflow-y: auto; height: 100vh">
      <a-layout-header>
        <a-popover
          :title="user && user.name"
          trigger="click"
          class="desktop-user-avatar"
        >
          <a-avatar :size="48">{{
            user && user.name.charAt(0).toUpperCase()
          }}</a-avatar>
          <template #content>
            <router-link to="/">
              <home-outlined />
              Ir para o site
            </router-link>
            <a-divider />
            <a-button @click="logout" type="ghost" danger block>
              <logout-outlined /> Sair
            </a-button>
          </template>
        </a-popover>
      </a-layout-header>
      <a-layout-content :style="{ margin: '24px 16px 0' }">
        <div
          :style="{
            padding: '16px',
            background: '#fff',
          }"
        >
          <router-view />
        </div>
      </a-layout-content>
      <a-layout-footer style="text-align: center">
        Rifandos &copy; 2022 - Todos os direitos reservados
      </a-layout-footer>
    </a-layout>
  </a-layout>
</template>

<script lang="ts">
import { storeToRefs } from "pinia";
import {
  SettingOutlined,
  ShopOutlined,
  BankOutlined,
  TeamOutlined,
  DollarCircleOutlined,
  LogoutOutlined,
  HomeOutlined,
} from "@ant-design/icons-vue";
import { defineComponent, ref } from "@vue/runtime-core";

import { useAuthStore } from "@/store/auth";

export default defineComponent({
  name: "PrivateLayout",
  components: {
    SettingOutlined,
    ShopOutlined,
    BankOutlined,
    TeamOutlined,
    DollarCircleOutlined,
    LogoutOutlined,
    HomeOutlined,
  },
  setup() {
    const selectedKeys = ref<string[]>(["/sorteios"]);
    const store = useAuthStore();
    const { user, isAdmin } = storeToRefs(store);

    return {
      selectedKeys,
      user,
      isAdmin,
      logout: store.logout,
    };
  },
});
</script>

<style lang="scss">
.logo {
  height: 32px;
  margin: 16px;

  & img {
    max-width: 100%;
  }
}

.ant-layout-header {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  padding: 10px;
  background: #fff;

  .desktop-user-avatar {
    cursor: pointer;
  }
}

.ant-layout-sider,
.ant-menu.ant-menu-dark,
.ant-layout-sider-zero-width-trigger {
  background: #44853c;
}

.ant-menu-dark.ant-menu-dark:not(.ant-menu-horizontal) .ant-menu-item-selected {
  background-color: #30532b;
}

.ant-menu-dark .ant-menu-inline.ant-menu-sub {
  background: #5f8f59;
}

@media (max-width: 414px) {
  .ant-layout-sider {
    position: fixed;
    height: 100%;
    z-index: 999;
  }
}

.site-layout-sub-header-background {
  background: #fff;
}

.site-layout-background {
  background: #fff;
}

[data-theme="dark"] .site-layout-sub-header-background {
  background: #141414;
}

.ant-page-header {
  padding: 0;
  margin-bottom: 1rem;
}

.ant-table-wrapper {
  margin: 1rem auto;
}
</style>