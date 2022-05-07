import Vue from "vue";
import VueRouter from "vue-router";

Vue.use(VueRouter);

import Home from "../views/Home";
import ContestList from "../views/ContestList";
import Profile from "../views/Profile";

const routes = [
    {
        name: "home",
        path: "/",
        component: Home,
    },
    {
        name: "contests",
        path: "/sorteios",
        component: ContestList,
    },
    {
        name: "profile",
        path: "/minha-conta",
        component: Profile,
    },
];

const router = new VueRouter({
    mode: "history",
    routes: routes,
});

export default router;
