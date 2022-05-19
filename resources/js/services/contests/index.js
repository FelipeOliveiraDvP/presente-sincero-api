import api from "../../api";

export async function listContests(params) {
    return api.get("contests", params);
}

export async function createContest(data) {
    return api.post("contests", data);
}
