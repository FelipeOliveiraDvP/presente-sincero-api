export default (error) => {
    console.log("Interceptor: ", error);

    return Promise.reject(error.response.data);
};
