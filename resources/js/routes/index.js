import Vue from "vue";
import VueRouter from "vue-router";

Vue.use(VueRouter);

import Home from "../views/Home";
import ContestList from "../views/ContestList";
import ContestDetail from "../views/ContestDetail";
import Checkout from "../views/Checkout";
import Profile from "../views/Profile";
import Login from "../views/Login";
import Register from "../views/Register";

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
        name: "contestDetail",
        path: "/sorteios/:slug",
        component: ContestDetail,
    },
    {
        name: "checkout",
        path: "/finalizar-compra",
        component: Checkout,
    },
    {
        name: "profile",
        path: "/minha-conta",
        component: Profile,
    },
    {
        name: "login",
        path: "/login",
        component: Login,
    },
    {
        name: "register",
        path: "/cadastre-se",
        component: Register,
    },
];

const router = new VueRouter({
    mode: "history",
    routes: routes,
});

export default router;
