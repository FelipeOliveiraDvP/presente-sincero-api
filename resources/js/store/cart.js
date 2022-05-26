export default {
    namespaced: true,
    state: {
        cart: {
            numbers: [],
            total: 0,
        },
    },
    getters: {
        cart(state) {
            return state.cart;
        },
    },
    mutations: {
        SET_CART(state, value) {
            state.cart = value;
        },
    },
    actions: {
        updateCart({ commit }, payload) {
            commit("SET_CART", { ...payload });
        },
    },
};
