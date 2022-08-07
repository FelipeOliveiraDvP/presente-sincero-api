import { ContestBankAccount, ContestSale } from "@/types/Contest.types";
import { defineStore } from "pinia";

export interface CartState {
  contestId: number;
  title: string;
  slug: string;
  username: string | string[];
  description: string;
  whatsappNumber: string;
  whatsappGroup?: string;
  price: number;
  quantity: number;
  sale: ContestSale | null;
  bankAccounts: ContestBankAccount[];
  total: number;
}

const state: CartState = {
  contestId: 0,
  title: "",
  slug: "",
  username: "",
  description: "",
  whatsappNumber: "",
  whatsappGroup: "",
  price: 0,
  quantity: 0,
  sale: null,
  bankAccounts: [],
  total: 0,
};

export const useCartStore = defineStore("cart", {
  state: () => ({
    ...state,
  }),
  getters: {
    hasOrder(state: CartState) {
      return (
        state.title !== "" ||
        state.slug !== "" ||
        state.username !== "" ||
        state.description !== "" ||
        state.whatsappNumber !== "" ||
        state.price > 0 ||
        state.quantity > 0 ||
        state.total > 0
      );
    },
  },
  actions: {
    saveCart(cart: CartState) {
      this.$patch({ ...cart });
    },
    emptyCart() {
      this.$reset();
    },
  },
});
