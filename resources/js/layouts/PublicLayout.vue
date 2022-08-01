<template>
  <a-layout style="min-height: 100vh" class="public-layout">
    <a-layout-header>
      <router-link to="/">
        <a-image :preview="false" src="/img/logo.png" :width="175" />
      </router-link>

      <a-popover
        v-if="authenticated"
        :title="`Olá ${user && user.name}`"
        trigger="click"
        class="desktop-user-avatar"
      >
        <a-avatar :size="48">
          {{ user && user.name.charAt(0).toUpperCase() }}
        </a-avatar>
        <template #content>
          <router-link v-if="isAdmin || isSeller" to="/admin/sorteios">
            <a-button type="link" block>
              <dashboard-outlined />
              Ir para a Dashboard
            </a-button>
          </router-link>
          <router-link to="/minha-conta">
            <a-button type="link" block>
              <user-outlined /> Minha conta
            </a-button>
          </router-link>
          <a-button @click="logout" type="link" danger block>
            <logout-outlined /> Sair
          </a-button>
        </template>
      </a-popover>

      <a-space v-else class="desktop-links">
        <router-link to="/login">
          <a-button type="primary" ghost> <login-outlined /> Entre </a-button>
        </router-link>

        <router-link to="/cadastre-se">
          <a-button type="primary" ghost>
            <user-outlined /> Cadastre-se
          </a-button>
        </router-link>
      </a-space>

      <a-button
        class="mobile-menu-button"
        type="primary"
        shape="circle"
        size="large"
        @click="showDrawer"
      >
        <menu-outlined />
      </a-button>

      <a-drawer
        v-model:visible="visible"
        :title="authenticated ? user && user.name : 'Bem vindo a Rifandos'"
      >
        <div v-if="authenticated">
          <router-link v-if="isAdmin || isSeller" to="/admin/sorteios">
            <a-button type="link" block>
              <dashboard-outlined />
              Ir para a Dashboard
            </a-button>
          </router-link>
          <router-link to="/minha-conta">
            <a-button type="link" block>
              <user-outlined /> Minha conta
            </a-button>
          </router-link>
          <a-button type="link" danger block>
            <logout-outlined /> Sair
          </a-button>
        </div>

        <div v-else>
          <p>Bem vindo a Rifandos, entre ou cadastre-se para continuar</p>

          <a-space>
            <router-link to="/login">
              <a-button type="primary" ghost>
                <login-outlined /> Entre
              </a-button>
            </router-link>

            <router-link to="/cadastre-se">
              <a-button type="primary" ghost>
                <user-outlined /> Cadastre-se
              </a-button>
            </router-link>
          </a-space>
        </div>
      </a-drawer>
    </a-layout-header>

    <a-layout-content>
      <router-view />
    </a-layout-content>

    <a-layout-footer>
      <a-row>
        <a-col :xs="24" :md="12" :lg="6">
          <div class="step-footer">
            <span class="step">1</span>
            <p>
              Escolha o prêmio que você deseja ganhar, clique em participar para
              ver os detalhes e os números disponíveis.
            </p>
          </div>
        </a-col>
        <a-col :xs="24" :md="12" :lg="6">
          <div class="step-footer">
            <span class="step">2</span>
            <p>Selecione os números que você deseja para concorrer ao prêmio</p>
          </div>
        </a-col>
        <a-col :xs="24" :md="12" :lg="6">
          <div class="step-footer">
            <span class="step">3</span>
            <p>
              Realize o pagamento através do PIX ou com o método de sua
              preferência. Depois é só enviar o comprovante e você já está
              concorrendo
            </p>
          </div>
        </a-col>
        <a-col :xs="24" :md="12" :lg="6">
          <div class="step-footer">
            <span class="step">4</span>
            <p>
              Agora é só acompanhar o sorteio e torcer! Você pode acompanhar o
              resultado na sua conta ou atrávés do grupo exclusivo de
              participantes
            </p>
          </div>
        </a-col>
      </a-row>
      <div class="copyright">
        Rifandos &copy; - 2022 - Todos os direitos reservados
      </div>
    </a-layout-footer>
  </a-layout>
</template>

<script lang="ts">
import { useAuthStore } from "@/store/auth";
import { defineComponent, ref } from "@vue/runtime-core";
import {
  UserOutlined,
  LoginOutlined,
  LogoutOutlined,
  MenuOutlined,
  DashboardOutlined,
} from "@ant-design/icons-vue";
import { storeToRefs } from "pinia";

export default defineComponent({
  name: "PublicLayout",
  components: {
    LoginOutlined,
    LogoutOutlined,
    UserOutlined,
    MenuOutlined,
    DashboardOutlined,
  },
  setup() {
    const store = useAuthStore();
    const visible = ref<boolean>(false);
    const { authenticated, user, isAdmin, isSeller } = storeToRefs(store);

    const showDrawer = () => {
      visible.value = !visible.value;
    };

    return {
      authenticated,
      user,
      visible,
      isAdmin,
      isSeller,
      showDrawer,
      logout: store.logout,
    };
  },
});
</script>

<style>
.public-layout .ant-layout-header {
  background-color: #fff;
  box-shadow: rgba(0, 0, 0, 0.16) 0px 1px 4px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 32px;
}

.ant-drawer-body .ant-btn.ant-btn-block,
.ant-popover-inner .ant-btn.ant-btn-block {
  text-align: left !important;
}

.desktop-user-avatar {
  display: block;
}

.ant-avatar {
  cursor: pointer;
}

.mobile-menu-button {
  display: none;
}

.ant-layout-content {
  min-height: 600px;
}

.public-layout .ant-layout-footer {
  color: #fff;
  background: #44853c;
}

.ant-layout-footer .step-footer {
  text-align: center;
  padding: 1rem;
}

.ant-layout-footer .step-footer .step {
  display: inline-block;
  background: #fff;
  color: #44853c;
  width: 48px;
  height: 48px;
  line-height: 48px;
  border-radius: 100%;
  font-size: 1.6rem;
  font-weight: 500;
}

.ant-layout-footer .copyright {
  text-align: center;
  font-size: 0.8rem;
}

@media (max-width: 768px) {
  .mobile-menu-button {
    display: block;
  }

  .desktop-links {
    display: none;
  }

  .desktop-user-avatar {
    display: none;
  }
}
</style>