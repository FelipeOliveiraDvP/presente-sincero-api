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
          id="mainNavbar"
          toggleable="lg"
          type="light"
          variant="light"
          fixed="top"
        >
          <header
            class="w-100 p-2 d-flex justify-content-between align-items-center"
          >
            <h1>LOGO</h1>
            <b-button variant="outline-danger">Sair</b-button>
          </header>
        </b-navbar>
      </template>

      <template v-slot:content>
        <b-container style="margin-top: 56px">
          <router-view />
        </b-container>
      </template>
    </bootstrap-sidebar>
  </div>
</template>

<script>
import BootstrapSidebar from "vue-bootstrap-sidebar";

export default {
  name: "PrivateLayout",
  components: {
    "bootstrap-sidebar": BootstrapSidebar,
  },
  data() {
    return {
      initialShow: true,
      header: "<h3>LOGO</h3>",
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
      ],
    };
  },
};
</script>

<style>
</style>