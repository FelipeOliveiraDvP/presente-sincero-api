import Vue from "vue";
import VueRouter from "vue-router";

Vue.use(VueRouter);

// Public
import Home from "../views/Home";
import ContestList from "../views/ContestList";
import ContestDetail from "../views/ContestDetail";
import Checkout from "../views/Checkout";
import Profile from "../views/Profile";
import Login from "../views/Login";
import Register from "../views/Register";
import Recovery from "../views/Recovery";
// Admin
import AdminContestList from "../views/admin/ContestList";
import AdminContestCreate from "../views/admin/ContestCreate";
import AdminContestDetail from "../views/admin/ContestDetail";
import AdminCustomerList from "../views/admin/CustomerList";
import AdminCustomerDetail from "../views/admin/CustomerDetail";

const routes = [
    // Public
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
        name: "login",
        path: "/login",
        component: Login,
    },
    {
        name: "register",
        path: "/cadastre-se",
        component: Register,
    },
    {
        name: "recovery",
        path: "/recuperar-senha",
        component: Recovery,
    },
    {
        name: "profile",
        path: "/minha-conta",
        component: Profile,
    },
    // Admin
    {
        name: "adminContestList",
        path: "/admin/sorteios",
        component: AdminContestList,
    },
    {
        name: "adminContestCreate",
        path: "/admin/sorteios/novo-sorteio",
        component: AdminContestCreate,
    },
    {
        name: "adminContestDetail",
        path: "/admin/sorteios/:id",
        component: AdminContestDetail,
    },
    {
        name: "adminCustomerList",
        path: "/admin/clientes",
        component: AdminCustomerList,
    },
    {
        name: "adminCustomerDetail",
        path: "/admin/clientes/:id",
        component: AdminCustomerDetail,
    },
];

const router = new VueRouter({
    mode: "history",
    routes: routes,
});

export default router;
