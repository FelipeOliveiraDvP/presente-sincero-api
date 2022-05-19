import api from "../../api";

export async function login(credentials) {
    return api.post("login", credentials);
}

export async function register(credentials) {
    return api.post("register", credentials);
}
