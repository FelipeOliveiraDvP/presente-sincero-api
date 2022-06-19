<template>
  <div>
    <div
      class="
        p-2
        border
        rounded
        d-flex
        justify-content-between
        align-items-center
      "
    >
      <p class="m-0">
        {{ selectedUser ? selectedUser.name : "Pesquisar clientes" }}
      </p>
      <div>
        <b-button
          variant="primary"
          size="sm"
          @click="$bvModal.show('users-select-modal')"
        >
          <font-awesome-icon icon="fa-solid fa-search" />
        </b-button>
        <b-button
          v-if="selectedUser !== null"
          variant="danger"
          size="sm"
          @click="handleClearFilter"
        >
          <font-awesome-icon icon="fa-solid fa-times" />
        </b-button>
      </div>
    </div>
    <b-modal id="users-select-modal" title="Pesquisar clientes" hide-footer>
      <div class="mb-3">
        <input
          id="whatsapp"
          class="form-control mb-2"
          type="number"
          placeholder="WhatsApp"
          v-model="search.whatsapp"
          @input="handleSearch"
        />
        <input
          id="name"
          class="form-control"
          type="text"
          placeholder="Nome do cliente"
          v-model="search.name"
          @input="handleSearch"
        />
      </div>
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
    </b-modal>
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
      search: {
        whatsapp: "",
        name: "",
      },
      users: [],
    };
  },
  mounted() {
    this.getUsersData();
  },
  methods: {
    async getUsersData() {
      this.loading = true;

      const result = await listUsers(this.search);

      this.users = result.data;
      this.loading = false;
    },
    handleSelectUser(user) {
      this.selectedUser = { ...user };
      this.$emit("select", user.id);
      this.$bvModal.hide("users-select-modal");
    },
    handleClearFilter() {
      this.selectedUser = null;
      this.$emit("clear");
    },
    handleSearch: debounce(async function (e) {
      const { id, value } = e.target;

      this.search[id] = value;

      await this.getUsersData();
    }, 500),
  },
};
</script>

<style>
</style>