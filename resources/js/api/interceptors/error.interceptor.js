export default (error) => {
  console.log("ErrorInterceptor: ", error.response.data);

  return Promise.reject(error.response.data);
};
