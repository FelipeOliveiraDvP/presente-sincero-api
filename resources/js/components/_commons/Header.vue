<template>
  <b-navbar
    class="site-header d-flex justify-content-between p-4"
    toggleable="lg"
    type="light"
    variant="light"
  >
    <b-container>
      <b-navbar-brand class="logo">
        <router-link to="/"> LOGO </router-link>
      </b-navbar-brand>

      <b-navbar-toggle target="nav-collapse">
        <i class="fas fa-bars"></i>
      </b-navbar-toggle>

      <b-collapse id="nav-collapse" is-nav class="justify-content-between">
        <b-navbar-nav>
          <b-nav-item>
            <router-link to="/sorteios">SORTEIOS</router-link>
          </b-nav-item>
        </b-navbar-nav>

        <b-navbar-nav class="account">
          <b-nav-item>
            <router-link to="/login"
              ><i class="fas fa-sign-in-alt"></i> ENTRE</router-link
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
export default {
  name: "Header",
};
</script>

<style>
</style>