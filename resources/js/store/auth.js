import router from "../routes";
import { Roles } from "../services/auth/enums";

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
        admin: false,
        token: null,
    },
    getters: {
        user(state) {
            return state.user;
        },
        admin(state) {
            return state.admin;
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
        SET_ADMIN(state, value) {
            state.admin = value;
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

            const { role_id } = user.role;

            if (role_id === Roles.Admin) {
                commit("SET_ADMIN", true);
                router.push({ name: "adminContestList" });
            } else {
                commit("SET_ADMIN", false);
                router.push({ name: "profile" });
            }
        },
        logout({ commit }) {
            commit("SET_USER", emptyUser);
            commit("SET_AUTHENTICATED", false);
            commit("SET_TOKEN", null);
            commit("SET_ADMIN", false);

            router.push({ name: "login" });
        },
        updateUser({ commit }, payload) {
            commit("UPDATE_USER", { ...payload });
        },
    },
};
