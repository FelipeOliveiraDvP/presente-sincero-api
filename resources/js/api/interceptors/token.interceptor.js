import store from "@/store";

export default (config) => {
  const { token } = store.state.auth;

  return {
    ...config,
    headers: {
      Authorization: `Bearer ${token}`,
    },
  };
};
