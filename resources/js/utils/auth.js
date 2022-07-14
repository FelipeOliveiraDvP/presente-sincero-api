import store from "../store";

export function isAdmin() {
  const { user } = store.state.auth;
  const { MIX_ADMIN_ROLE } = process.env;

  return user.role === MIX_ADMIN_ROLE;
}

export function isSeller() {
  const { user } = store.state.auth;
  const { MIX_SELLER_ROLE } = process.env;

  return user.role === MIX_SELLER_ROLE;
}

export function isSellerOrAdmin() {
  const { user } = store.state.auth;
  const { MIX_ADMIN_ROLE, MIX_SELLER_ROLE } = process.env;

  return user.role === MIX_ADMIN_ROLE || user.role === MIX_SELLER_ROLE;
}
