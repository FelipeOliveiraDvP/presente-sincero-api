<template>
  <div class="relative">
    <div class="input-group">
      <input
        class="form-control"
        type="text"
        placeholder="Pesquisar clientes por nome, WhatsApp ou e-mail"
        v-model="search"
        @input="handleSearch"
      />
      <b-button variant="danger" size="sm" @click="handleClearFilter">
        <font-awesome-icon icon="fa-solid fa-times" />
      </b-button>
    </div>

    <div
      class="absolute bg-white p-2 rounded text-black mt-1"
      v-if="showResults"
    >
      <p>Resultados da pesquisa</p>
      <my-loader v-if="loading" />
      <p
        class="text-muted"
        v-else-if="users.length === 0"
        style="min-height: 200px"
      >
        Não foram encontrados clientes
      </p>
      <b-list-group v-else>
        <b-list-group-item
          v-for="user in users"
          :key="user.id"
          button
          @click="handleSelectUser(user)"
          >{{ user.name }}
        </b-list-group-item>
      </b-list-group>
    </div>
  </div>
</template>

<script>
import debounce from "lodash.debounce";
import LoaderVue from "@/components/_commons/Loader.vue";
import { listUsers } from "@/services/users";

export default {
  name: "UsersSelect",
  components: {
    "my-loader": LoaderVue,
  },
  data() {
    return {
      loading: false,
      selectedUser: null,
      showResults: false,
      search: "",
      users: [],
    };
  },
  mounted() {
    this.getUsersData();
  },
  methods: {
    async getUsersData() {
      this.loading = true;

      const result = await listUsers({ search: this.search });

      this.users = result.data;
      this.loading = false;
    },
    handleSelectUser(user) {
      this.selectedUser = { ...user };
      this.showResults = false;
      this.$emit("select", user.id);
    },
    handleClearFilter() {
      this.selectedUser = null;
      this.showResults = false;
      this.$emit("clear");
    },
    handleSearch: debounce(async function (e) {
      const { value } = e.target;

      this.search = value;
      this.showResults = true;

      await this.getUsersData();
    }, 500),
  },
};
</script>

<style>
</style>