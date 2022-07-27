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
      this.$state = { ...payment };
    },
    resetPayment() {
      this.$state = { ...state };
    },
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

// enum PaymentMutationsEnum {
//   SET_USER_ID = "SET_USER_ID",
//   SET_ORDER_ID = "SET_ORDER_ID",
//   SET_CONFIRMED = "SET_CONFIRMED",
// }

// enum PaymentActionsEnum {
//   CONFIRM_PAYMENT = "CONFIRM_PAYMENT",
//   RESET_PAYMENT = "RESET_PAYMENT",
// }

// export interface PaymentMutations<S = PaymentState> {
//   [PaymentMutationsEnum.SET_USER_ID](state: S, value: number | null): void;
//   [PaymentMutationsEnum.SET_ORDER_ID](state: S, value: number | null): void;
//   [PaymentMutationsEnum.SET_CONFIRMED](state: S, value: boolean): void;
// }

// export interface PaymentGetters {
//   userId(state: PaymentState): number | null;
//   orderId(state: PaymentState): number | null;
//   confirmed(state: PaymentState): boolean;
// }

// type PaymentActionContext = {
//   commit<K extends keyof PaymentMutations>(
//     key: K,
//     payload: Parameters<PaymentMutations[K]>[1] | null
//   ): ReturnType<PaymentMutations[K]>;
// } & Omit<ActionContext<PaymentState, RootState>, "commit">;

// export interface PaymentActions {
//   [PaymentActionsEnum.CONFIRM_PAYMENT](
//     { commit }: PaymentActionContext,
//     payload: PaymentState
//   ): void;
//   [PaymentActionsEnum.RESET_PAYMENT]({ commit }: PaymentActionContext): void;
// }

// export const state: PaymentState = {
//   userId: null,
//   orderId: null,
//   confirmed: false,
// };

// const mutations: MutationTree<PaymentState> & PaymentMutations = {
//   [PaymentMutationsEnum.SET_USER_ID](
//     state: PaymentState,
//     payload: number | null
//   ) {
//     state.userId = payload;
//   },
//   [PaymentMutationsEnum.SET_ORDER_ID](
//     state: PaymentState,
//     payload: number | null
//   ) {
//     state.orderId = payload;
//   },
//   [PaymentMutationsEnum.SET_CONFIRMED](state: PaymentState, payload: boolean) {
//     state.confirmed = payload;
//   },
// };

// const getters: GetterTree<PaymentState, RootState> & PaymentGetters = {
//   userId: (state) => state.userId,
//   orderId: (state) => state.orderId,
//   confirmed: (state) => state.confirmed,
// };

// const actions: ActionTree<PaymentState, RootState> & PaymentActions = {
//   [PaymentActionsEnum.CONFIRM_PAYMENT]({ commit }, payload) {
//     const { userId, orderId, confirmed } = payload;

//     commit(PaymentMutationsEnum.SET_USER_ID, userId);
//     commit(PaymentMutationsEnum.SET_ORDER_ID, orderId);
//     commit(PaymentMutationsEnum.SET_CONFIRMED, confirmed);
//   },
//   [PaymentActionsEnum.RESET_PAYMENT]({ commit }) {
//     commit(PaymentMutationsEnum.SET_USER_ID, null);
//     commit(PaymentMutationsEnum.SET_ORDER_ID, null);
//     commit(PaymentMutationsEnum.SET_CONFIRMED, false);
//   },
// };

// export type PaymentStore<S = PaymentState> = Omit<
//   Store<S>,
//   "getters" | "commit" | "dispatch"
// > & {
//   commit<
//     K extends keyof PaymentMutations,
//     P extends Parameters<PaymentMutations[K]>[1]
//   >(
//     key: K,
//     payload: P,
//     options?: CommitOptions
//   ): ReturnType<PaymentMutations[K]>;
// } & {
//   dispatch<K extends keyof PaymentActions>(
//     key: K,
//     payload: Parameters<PaymentActions[K]>[1],
//     options?: CommitOptions
//   ): ReturnType<PaymentActions[K]>;
// } & {
//   getters: {
//     [K in keyof PaymentGetters]: ReturnType<PaymentGetters[K]>;
//   };
// };

// export const store: Module<PaymentState, RootState> = {
//   state,
//   mutations,
//   getters,
//   actions,
// };
