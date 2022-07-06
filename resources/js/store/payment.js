export default {
  namespaced: true,
  state: {
    userId: null,
    orderId: null,
    confirmed: false,
  },
  getters: {
    userId(state) {
      return state.userId;
    },
    orderId(state) {
      return state.orderId;
    },
    confirmed(state) {
      return state.confirmed;
    },
  },
  mutations: {
    SET_USER_ID(state, value) {
      state.userId = value;
    },
    SET_ORDER_ID(state, value) {
      state.orderId = value;
    },
    SET_CONFIRMED(state, value) {
      state.confirmed = value;
    },
  },
  actions: {
    confirmPayment({ commit }, payload) {
      const { userId, orderId, confirmed } = { ...payload };

      commit("SET_USER_ID", userId);
      commit("SET_ORDER_ID", orderId);
      commit("SET_CONFIRMED", confirmed);
    },
    resetPayment({ commit }) {
      commit("SET_USER_ID", null);
      commit("SET_ORDER_ID", null);
      commit("SET_CONFIRMED", false);
    },
  },
};
