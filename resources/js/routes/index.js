import Vue from "vue";
import VueRouter from "vue-router";

import store from "../store";

Vue.use(VueRouter);

const routes = [
  // Public
  {
    name: "public",
    path: "/",
    component: () =>
      import(/* webpackChunkName: "PublicLayout" */ "@/layouts/PublicLayout"),
    children: [
      {
        name: "home",
        path: "/",
        component: () => import(/* webpackChunkName: "Home" */ "@/views/Home"),
      },
      {
        name: "contests",
        path: "/sorteios",
        component: () =>
          import(/* webpackChunkName: "ContestList" */ "@/views/ContestList"),
      },
      {
        name: "contestDetail",
        path: "/sorteios/:slug",
        component: () =>
          import(
            /* webpackChunkName: "ContestDetail" */ "@/views/ContestDetail"
          ),
      },
      {
        name: "checkout",
        path: "/finalizar-compra",
        component: () =>
          import(/* webpackChunkName: "Checkout" */ "@/views/Checkout"),
        meta: {
          checkout: true,
        },
      },
      {
        name: "login",
        path: "/login",
        component: () =>
          import(/* webpackChunkName: "Login" */ "@/views/Login"),
        meta: {
          restricted: true,
        },
      },
      {
        name: "register",
        path: "/cadastre-se",
        component: () =>
          import(/* webpackChunkName: "Register" */ "@/views/Register"),
        meta: {
          restricted: true,
        },
      },
      {
        name: "recovery",
        path: "/recuperar-senha",
        component: () =>
          import(/* webpackChunkName: "Recovery" */ "@/views/Recovery"),
        meta: {
          restricted: true,
        },
      },
      {
        name: "verify",
        path: "/verificar-codigo",
        component: () =>
          import(/* webpackChunkName: "Verify" */ "@/views/Verify"),
        meta: {
          restricted: true,
        },
      },
      {
        name: "reset",
        path: "/nova-senha",
        component: () =>
          import(/* webpackChunkName: "Reset" */ "@/views/Reset"),
        meta: {
          restricted: true,
        },
      },
      {
        name: "profile",
        path: "/minha-conta",
        component: () =>
          import(/* webpackChunkName: "Profile" */ "@/views/Profile"),
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
    component: () =>
      import(/* webpackChunkName: "PrivateLayout" */ "@/layouts/PrivateLayout"),
    meta: {
      adminRoute: true,
    },
    children: [
      {
        name: "adminContestList",
        path: "/admin/sorteios",
        component: () =>
          import(
            /* webpackChunkName: "AdminContestList" */ "@/views/admin/ContestList"
          ),
        meta: {
          adminRoute: true,
        },
      },
      {
        name: "adminContestCreate",
        path: "/admin/sorteios/novo-sorteio",
        component: () =>
          import(
            /* webpackChunkName: "AdminContestCreate" */ "@/views/admin/ContestCreate"
          ),
        meta: {
          adminRoute: true,
        },
      },
      {
        name: "adminContestDetail",
        path: "/admin/sorteios/:id",
        component: () =>
          import(
            /* webpackChunkName: "AdminContestDetail" */ "@/views/admin/ContestDetail"
          ),
        meta: {
          adminRoute: true,
        },
      },
      {
        name: "adminContestManage",
        path: "/admin/sorteios/:id/gerenciar",
        component: () =>
          import(
            /* webpackChunkName: "AdminContestManage" */ "@/views/admin/ContestManage"
          ),
        meta: {
          adminRoute: true,
        },
      },
      {
        name: "adminContestOrders",
        path: "/admin/sorteios/:id/pedidos",
        component: () =>
          import(
            /* webpackChunkName: "AdminContestOrders" */ "@/views/admin/ContestOrders"
          ),
        meta: {
          adminRoute: true,
        },
      },
      {
        name: "adminCustomerList",
        path: "/admin/clientes",
        component: () =>
          import(
            /* webpackChunkName: "AdminCustomerList" */ "@/views/admin/CustomerList"
          ),
        meta: {
          adminRoute: true,
        },
      },
      {
        name: "adminCustomerDetail",
        path: "/admin/clientes/:id",
        component: () =>
          import(
            /* webpackChunkName: "AdminCustomerDetail" */ "@/views/admin/CustomerDetail"
          ),
        meta: {
          adminRoute: true,
        },
      },
      {
        name: "adminBankAccountList",
        path: "/admin/contas",
        component: () =>
          import(
            /* webpackChunkName: "AdminBankAccountList" */ "@/views/admin/BankAccountList"
          ),
        meta: {
          adminRoute: true,
        },
      },
      {
        name: "adminAccountManage",
        path: "/admin/minha-conta",
        component: () =>
          import(/* webpackChunkName: "Profile" */ "@/views/Profile"),
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
