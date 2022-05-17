import api from "../../api";

export async function listContests(params) {
    return api.get("contests", params);
}
