<template>
  <b-navbar
    class="d-flex justify-content-between p-4"
    toggleable="lg"
    type="dark"
    variant="dark"
  >
    <b-container>
      <router-link to="/">
        <b-navbar-brand> LOGO </b-navbar-brand>
      </router-link>

      <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

      <b-collapse id="nav-collapse" is-nav class="justify-content-between">
        <b-navbar-nav>
          <b-nav-item>
            <router-link to="/sorteios">SORTEIOS</router-link>
          </b-nav-item>
        </b-navbar-nav>

        <b-navbar-nav>
          <b-nav-item>
            <router-link to="/login">ENTRE</router-link>
          </b-nav-item>
          <b-nav-item>
            <router-link to="/cadastre-se">CADASTRE-SE</router-link>
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