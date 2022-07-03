import Vue from "vue";
import VueRouter from "vue-router";

import store from "../store";

Vue.use(VueRouter);

// Layouts
import PublicLayout from "../layouts/PublicLayout";
import PrivateLayout from "../layouts/PrivateLayout";

// Public
import Home from "../views/Home";
import ContestList from "../views/ContestList";
import ContestDetail from "../views/ContestDetail";
import Checkout from "../views/Checkout";
import Profile from "../views/Profile";
import Login from "../views/Login";
import Register from "../views/Register";
import Recovery from "../views/Recovery";
import Verify from "../views/Verify";
import Reset from "../views/Reset";

// Admin
import AdminContestList from "../views/admin/ContestList";
import AdminContestCreate from "../views/admin/ContestCreate";
import AdminContestDetail from "../views/admin/ContestDetail";
import AdminContestManage from "../views/admin/ContestManage";
import AdminContestOrders from "../views/admin/ContestOrders";
import AdminCustomerList from "../views/admin/CustomerList";
import AdminCustomerDetail from "../views/admin/CustomerDetail";
import AdminBankAccountList from "../views/admin/BankAccountList";

const routes = [
  // Public
  {
    name: "public",
    path: "/",
    component: PublicLayout,
    children: [
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
        meta: {
          checkout: true,
        },
      },
      {
        name: "login",
        path: "/login",
        component: Login,
        meta: {
          restricted: true,
        },
      },
      {
        name: "register",
        path: "/cadastre-se",
        component: Register,
        meta: {
          restricted: true,
        },
      },
      {
        name: "recovery",
        path: "/recuperar-senha",
        component: Recovery,
        meta: {
          restricted: true,
        },
      },
      {
        name: "verify",
        path: "/verificar-codigo",
        component: Verify,
        meta: {
          restricted: true,
        },
      },
      {
        name: "reset",
        path: "/nova-senha",
        component: Reset,
        meta: {
          restricted: true,
        },
      },
      {
        name: "profile",
        path: "/minha-conta",
        component: Profile,
        meta: {
          logged: true,
        },
      },
    ],
  },
  // Admin
  {
    name: "admin",
    path: "/admin",
    component: PrivateLayout,
    meta: {
      adminRoute: true,
    },
    children: [
      {
        name: "adminContestList",
        path: "/admin/sorteios",
        component: AdminContestList,
        meta: {
          adminRoute: true,
        },
      },
      {
        name: "adminContestCreate",
        path: "/admin/sorteios/novo-sorteio",
        component: AdminContestCreate,
        meta: {
          adminRoute: true,
        },
      },
      {
        name: "adminContestDetail",
        path: "/admin/sorteios/:id",
        component: AdminContestDetail,
        meta: {
          adminRoute: true,
        },
      },
      {
        name: "adminContestManage",
        path: "/admin/sorteios/:id/gerenciar",
        component: AdminContestManage,
        meta: {
          adminRoute: true,
        },
      },
      {
        name: "adminContestOrders",
        path: "/admin/sorteios/:id/pedidos",
        component: AdminContestOrders,
        meta: {
          adminRoute: true,
        },
      },
      {
        name: "adminCustomerList",
        path: "/admin/clientes",
        component: AdminCustomerList,
        meta: {
          adminRoute: true,
        },
      },
      {
        name: "adminCustomerDetail",
        path: "/admin/clientes/:id",
        component: AdminCustomerDetail,
        meta: {
          adminRoute: true,
        },
      },
      {
        name: "adminBankAccountList",
        path: "/admin/contas",
        component: AdminBankAccountList,
        meta: {
          adminRoute: true,
        },
      },
      {
        name: "adminAccountManage",
        path: "/admin/minha-conta",
        component: Profile,
        meta: {
          adminRoute: true,
        },
      },
    ],
  },
];

const router = new VueRouter({
  mode: "history",
  routes: routes,
});

router.beforeEach((to, from, next) => {
  const { authenticated, admin } = store.state.auth;
  const { adminRoute, logged, restricted, checkout } = to.meta;

  if (authenticated && restricted) {
    next({ name: "profile" });
  } else if (authenticated && adminRoute && !admin) {
    next({ name: "profile" });
  } else if (authenticated && restricted && admin) {
    next({ name: "adminContestList" });
  } else if (!authenticated && checkout) {
    next({ name: "contests" });
  } else if (!authenticated && (logged || adminRoute)) {
    next({ name: "login" });
  }

  window.scrollTo(0, 0);
  next();
});

export default router;
