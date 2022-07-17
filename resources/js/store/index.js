import Vue from "vue";
import Vuex from "vuex";
import createPersistedState from "vuex-persistedstate";

import auth from "./auth";
import payment from "./payment";

Vue.use(Vuex);

export default new Vuex.Store({
  plugins: [createPersistedState({ key: "PS_APP", paths: ["auth"] })],
  modules: {
    auth,
    payment,
  },
});
