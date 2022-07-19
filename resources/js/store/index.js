import { createStore } from "vuex";
import createPersistedState from "vuex-persistedstate";

import auth from "./auth";
import payment from "./payment";

const store = createStore({
  plugins: [createPersistedState({ key: "RIFANDOS", paths: ["auth"] })],
  modules: {
    auth,
    payment,
  },
});

export default store;
