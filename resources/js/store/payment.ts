import { defineStore } from "pinia";

export interface PaymentState {
  userId: number | null;
  orderId: number | null;
  confirmed: boolean;
}

const state: PaymentState = {
  userId: null,
  orderId: null,
  confirmed: false,
};

export const usePaymentStore = defineStore("payment", {
  state: () => ({
    ...state,
  }),
  actions: {
    confirmPayment(payment: PaymentState) {
      this.$patch({ ...payment });
    },
    resetPayment() {
      this.$reset();
    },
  },
});
