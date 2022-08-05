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
const MIX_CUSTOMER_ROLE = process.env.MIX_CUSTOMER_ROLE;

export const useAuthStore = defineStore("auth", {
  state: () => ({
    ...state,
  }),
  getters: {
    isAdmin(state: AuthState) {
      if (state.user === null) return false;

      const { role } = state.user as AuthUser;

      return role === MIX_ADMIN_ROLE;
    },
    isSeller(state: AuthState) {
      if (state.user === null) return false;

      const { role } = state.user as AuthUser;

      return role === MIX_SELLER_ROLE;
    },
    isCustomer(state: AuthState) {
      if (state.user === null) return false;

      const { role } = state.user as AuthUser;

      return role === MIX_CUSTOMER_ROLE;
    },
  },
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
