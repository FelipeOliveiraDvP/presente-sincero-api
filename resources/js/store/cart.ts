import { ContestSale } from "@/types/Contest.types";
import { defineStore } from "pinia";

export interface CartState {
  title: string;
  slug: string;
  username: string;
  description: string;
  price: number;
  quantity: number;
  sale: ContestSale | null;
  total: number;
}

const state: CartState = {
  title: "",
  slug: "",
  username: "",
  description: "",
  price: 0,
  quantity: 0,
  sale: null,
  total: 0,
};

export const useCartStore = defineStore("cart", {
  state: () => ({
    ...state,
  }),
  actions: {
    saveCart(cart: CartState) {
      this.$patch({ ...cart });
    },
    emptyCart() {
      this.$reset();
    },
  },
});
