<template>
  <div>
    <bootstrap-sidebar
      :initial-show="initialShow"
      :links="links"
      :header="header"
      :fa="true"
    >
      <template v-slot:navbar>
        <b-navbar
          class="private-sidebar"
          id="mainNavbar"
          toggleable="lg"
          fixed="top"
        >
          <header
            class="w-100 p-2 d-flex justify-content-between align-items-center"
          >
            <div></div>
            <b-button variant="outline-danger" @click="logout">Sair</b-button>
          </header>
        </b-navbar>
      </template>

      <template v-slot:content>
        <b-container fluid style="margin-top: 90px">
          <router-view />
        </b-container>
      </template>
    </bootstrap-sidebar>
  </div>
</template>

<script>
import BootstrapSidebar from "vue-bootstrap-sidebar";
import { mapActions } from "vuex";

export default {
  name: "PrivateLayout",
  components: {
    "bootstrap-sidebar": BootstrapSidebar,
  },
  data() {
    return {
      initialShow: false,
      header: "<h3 class='mb-0'>LOGO</h3>",
      links: [
        {
          name: "Sorteios",
          faIcon: ["fas", "fa-trophy"],
          children: [
            {
              name: "Todos os sorteios",
              href: {
                name: "adminContestList",
              },
            },
            {
              name: "Novo sorteio",
              href: { name: "adminContestCreate" },
            },
          ],
        },
        {
          name: "Clientes",
          href: { name: "adminCustomerList" },
          faIcon: ["fas", "fa-users"],
        },
        {
          name: "Contas bancárias",
          href: { name: "adminBankAccountList" },
          faIcon: ["fas", "fa-building-columns"],
        },
        {
          name: "Minha conta",
          href: { name: "adminAccountManage" },
          faIcon: ["fas", "fa-user"],
        },
      ],
    };
  },
  methods: {
    ...mapActions({
      signOut: "auth/logout",
    }),
    logout() {
      this.signOut();
    },
  },
};
</script>

<style lang="scss">
div#sidebarButton.sidebar-button.default-theme.cross.visible {
  margin-top: 0 !important;
}
#content.sidebar {
  margin-left: 0;
}
</style>