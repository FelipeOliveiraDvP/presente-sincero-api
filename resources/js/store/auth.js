import router from "../routes";

const emptyUser = {
    role: null,
    avatar: {
        path: "/assets/img/avatar.png",
    },
};

export default {
    namespaced: true,
    state: {
        user: emptyUser,
        authenticated: false,
        token: null,
    },
    getters: {
        user(state) {
            return state.user;
        },
        authenticated(state) {
            return state.authenticated;
        },
        token(state) {
            return state.token;
        },
    },
    mutations: {
        SET_USER(state, value) {
            state.user = value;
        },
        SET_AUTHENTICATED(state, value) {
            state.authenticated = value;
        },
        SET_TOKEN(state, value) {
            state.token = value;
        },
        UPDATE_USER(state, value) {
            state.user = { ...state.user, ...value };
        },
    },
    actions: {
        login({ commit }, payload) {
            const { user, token } = { ...payload };

            commit("SET_USER", user);
            commit("SET_AUTHENTICATED", true);
            commit("SET_TOKEN", token);

            router.push({ name: "profile" });
        },
        logout({ commit }) {
            commit("SET_USER", emptyUser);
            commit("SET_AUTHENTICATED", false);
            commit("SET_TOKEN", null);

            router.push({ name: "login" });
        },
        updateUser({ commit }, payload) {
            commit("UPDATE_USER", { ...payload });
        },
    },
};
