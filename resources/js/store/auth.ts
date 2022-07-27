import { defineStore } from "pinia";

import { AuthResponse, AuthUser } from "@/types/Auth.types";
import router from "@/routes";

export interface AuthState {
  user: AuthUser | null;
  authenticated: boolean;
  admin: boolean;
  token: string | null;
}

const state: AuthState = {
  user: null,
  authenticated: false,
  admin: false,
  token: null,
};

const MIX_ADMIN_ROLE = process.env.MIX_ADMIN_ROLE;
const MIX_SELLER_ROLE = process.env.MIX_SELLER_ROLE;

export const useAuthStore = defineStore("auth", {
  state: () => ({
    ...state,
  }),
  actions: {
    login(data: AuthResponse) {
      const { user, token } = data;
      const canManageContests =
        user.role === MIX_ADMIN_ROLE || user.role === MIX_SELLER_ROLE;

      this.$patch({
        authenticated: true,
        user: { ...user },
        token: token,
      });

      if (canManageContests) {
        this.$patch({ admin: true });
        router.push({ name: "adminContestList" });
      } else {
        router.push({ name: "profile" });
      }
    },
    simpleLogin(data: AuthResponse) {
      const { user, token } = data;

      this.$patch({
        authenticated: true,
        user: { ...user },
        token: token,
      });
    },
    logout() {
      this.$reset();
      router.push({ name: "login" });
    },
    updateUser(user: AuthUser) {
      this.$patch({ user: { ...user } });
    },
  },
  persist: {
    enabled: true,
    strategies: [
      {
        key: "RIFANDOS",
        storage: localStorage,
      },
    ],
  },
});
// import {
//   ActionContext,
//   ActionTree,
//   CommitOptions,
//   GetterTree,
//   Module,
//   MutationTree,
//   Store,
// } from "vuex";
// import { RootState } from ".";
// import { AuthResponse, AuthUser } from "@/services/auth/Auth.types";
// import router from "@/routes";

// export const AUTH_STORAGE_KEY = "RIFANDOS";

// export enum AuthMutationsEnum {
//   SET_USER = "SET_USER",
//   SET_AUTHENTICATED = "SET_AUTHENTICATED",
//   SET_ADMIN = "SET_ADMIN",
//   SET_TOKEN = "SET_TOKEN",
// }

// export enum AuthActionsEnum {
//   LOGIN = "LOGIN",
//   SIMPLE_LOGIN = "SIMPLE_LOGIN",
//   LOGOUT = "LOGOUT",
//   UPDATE_USER = "UPDATE_USER",
// }

// export interface AuthMutations<S = AuthState> {
//   [AuthMutationsEnum.SET_USER](state: S, value: AuthUser | null): void;
//   [AuthMutationsEnum.SET_AUTHENTICATED](state: S, value: boolean): void;
//   [AuthMutationsEnum.SET_ADMIN](state: S, value: boolean): void;
//   [AuthMutationsEnum.SET_TOKEN](state: S, value: string | null): void;
// }

// export interface AuthGetters {
//   user: (state: AuthState) => AuthUser | null;
//   authenticated: (state: AuthState) => boolean;
//   admin: (state: AuthState) => boolean;
//   token: (state: AuthState) => string | null;
// }

// type AuthActionContext = {
//   commit<K extends keyof AuthMutations>(
//     key: K,
//     payload: Parameters<AuthMutations[K]>[1] | null
//   ): ReturnType<AuthMutations[K]>;
// } & Omit<ActionContext<AuthState, RootState>, "commit">;

// export interface AuthActions {
//   [AuthActionsEnum.LOGIN](
//     { commit }: AuthActionContext,
//     payload: AuthResponse
//   ): void;
//   [AuthActionsEnum.SIMPLE_LOGIN](
//     { commit }: AuthActionContext,
//     payload: AuthResponse
//   ): void;
//   [AuthActionsEnum.LOGOUT]({ commit }: AuthActionContext): void;
//   [AuthActionsEnum.UPDATE_USER](
//     { commit }: AuthActionContext,
//     payload: AuthUser
//   ): void;
// }

// const mutations: MutationTree<AuthState> & AuthMutations = {
//   [AuthMutationsEnum.SET_USER](state: AuthState, payload: AuthUser | null) {
//     state.user = payload;
//   },
//   [AuthMutationsEnum.SET_AUTHENTICATED](state: AuthState, payload: boolean) {
//     state.authenticated = payload;
//   },
//   [AuthMutationsEnum.SET_ADMIN](state: AuthState, payload: boolean) {
//     state.admin = payload;
//   },
//   [AuthMutationsEnum.SET_TOKEN](state: AuthState, payload: string | null) {
//     state.token = payload;
//   },
// };

// const getters: GetterTree<AuthState, RootState> & AuthGetters = {
//   user: (value) => value.user,
//   authenticated: (state) => state.authenticated,
//   admin: (state) => state.admin,
//   token: (state) => state.token,
// };

// const actions: ActionTree<AuthState, RootState> & AuthActions = {
//   [AuthActionsEnum.LOGIN](
//     { commit }: AuthActionContext,
//     payload: AuthResponse
//   ) {
//     const { user, token } = { ...payload };

//     commit(AuthMutationsEnum.SET_USER, user);
//     commit(AuthMutationsEnum.SET_AUTHENTICATED, true);
//     commit(AuthMutationsEnum.SET_TOKEN, token);

//     const { MIX_ADMIN_ROLE, MIX_SELLER_ROLE } = process.env;
//     const canManageContests =
//       user.role === MIX_ADMIN_ROLE || user.role === MIX_SELLER_ROLE;

//     if (canManageContests) {
//       commit(AuthMutationsEnum.SET_ADMIN, true);
//       router.push({ name: "adminContestList" });
//     } else {
//       commit(AuthMutationsEnum.SET_ADMIN, false);
//       router.push({ name: "profile" });
//     }
//   },
//   [AuthActionsEnum.SIMPLE_LOGIN](
//     { commit }: AuthActionContext,
//     payload: AuthResponse
//   ) {
//     const { user, token } = { ...payload };

//     commit(AuthMutationsEnum.SET_USER, user);
//     commit(AuthMutationsEnum.SET_AUTHENTICATED, true);
//     commit(AuthMutationsEnum.SET_TOKEN, token);
//   },
//   [AuthActionsEnum.LOGOUT]({ commit }: AuthActionContext) {
//     commit(AuthMutationsEnum.SET_USER, null);
//     commit(AuthMutationsEnum.SET_AUTHENTICATED, false);
//     commit(AuthMutationsEnum.SET_TOKEN, null);
//     commit(AuthMutationsEnum.SET_ADMIN, false);

//     window.localStorage.removeItem(AUTH_STORAGE_KEY);

//     router.push({ name: "login" });
//   },
//   [AuthActionsEnum.UPDATE_USER](
//     { commit }: AuthActionContext,
//     payload: AuthUser
//   ) {
//     commit(AuthMutationsEnum.SET_USER, payload);
//   },
// };

// export type AuthStore<S = AuthState> = Omit<
//   Store<S>,
//   "getters" | "commit" | "dispatch"
// > & {
//   commit<
//     K extends keyof AuthMutations,
//     P extends Parameters<AuthMutations[K]>[1]
//   >(
//     key: K,
//     payload: P,
//     options?: CommitOptions
//   ): ReturnType<AuthMutations[K]>;
// } & {
//   dispatch<K extends keyof AuthActions>(
//     key: K,
//     payload?: Parameters<AuthActions[K]>[1],
//     options?: CommitOptions
//   ): ReturnType<AuthActions[K]>;
// } & {
//   getters: {
//     [K in keyof AuthGetters]: ReturnType<AuthGetters[K]>;
//   };
// };

// export const store: Module<AuthState, RootState> = {
//   state,
//   mutations,
//   getters,
//   actions,
// };
