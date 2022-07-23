import router from "@/routes";

export default (error) => {
  // console.log("ErrorInterceptor: ", error.response);

  if (error.response.status === 404) {
    router.push("/404");
  }

  return Promise.reject(error.response.data);
};
