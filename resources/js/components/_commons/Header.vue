<template>
  <b-navbar
    class="site-header d-flex justify-content-between p-4"
    toggleable="lg"
    type="light"
    variant="light"
  >
    <b-container>
      <b-navbar-brand class="logo">
        <router-link to="/">
          <img
            src="/img/logo.png"
            alt="Rifandos"
            class="img-fluid"
            style="max-width: 200px"
          />
        </router-link>
      </b-navbar-brand>

      <b-navbar-toggle target="nav-collapse">
        <i class="fas fa-bars"></i>
      </b-navbar-toggle>

      <b-collapse id="nav-collapse" is-nav class="justify-content-between">
        <div></div>
        <!-- <b-navbar-nav>
          <b-nav-item>
            <router-link to="/sorteios">SORTEIOS</router-link>
          </b-nav-item>
        </b-navbar-nav> -->

        <b-navbar-nav class="account" v-if="authenticated">
          <b-nav-item>
            <router-link to="/minha-conta"
              >Bem vindo {{ user.name }}</router-link
            >
          </b-nav-item>

          <b-nav-item>
            <b-button size="sm" variant="outline-danger" @click="signOut"
              >Sair</b-button
            >
          </b-nav-item>
        </b-navbar-nav>

        <b-navbar-nav class="account" v-else>
          <b-nav-item>
            <router-link to="/login">
              <font-awesome-icon icon="fa-sign-in-alt" />
              ENTRE</router-link
            >
          </b-nav-item>
          <b-nav-item>
            <router-link to="/cadastre-se"
              ><i class="fas fa-user"></i> CADASTRE-SE</router-link
            >
          </b-nav-item>
        </b-navbar-nav>
      </b-collapse>
    </b-container>
  </b-navbar>
</template>

<script>
import { mapActions } from "vuex";

export default {
  name: "Header",
  computed: {
    user() {
      return this.$store.state.auth.user;
    },
    authenticated() {
      return this.$store.state.auth.authenticated;
    },
  },
  methods: {
    ...mapActions({
      signOut: "auth/logout",
    }),
  },
};
</script>

<style>
</style>