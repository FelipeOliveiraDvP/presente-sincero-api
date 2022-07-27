import { useAuthStore } from "@/store/auth";
import { createWebHistory, createRouter } from "vue-router";

const routes = [
  // Public
  {
    name: "public",
    path: "/",
    component: () =>
      import(
        /* webpackChunkName: "PublicLayout" */ "@/layouts/PublicLayout.vue"
      ),
    children: [
      {
        name: "home",
        path: "/",
        component: () =>
          import(/* webpackChunkName: "Home" */ "@/views/Home.vue"),
      },
      {
        name: "contests",
        path: "/:username",
        component: () =>
          import(
            /* webpackChunkName: "ContestList" */ "@/views/ContestList.vue"
          ),
      },
      {
        name: "contestDetail",
        path: "/:username/:slug",
        component: () =>
          import(
            /* webpackChunkName: "ContestDetail" */ "@/views/ContestDetail.vue"
          ),
      },
      {
        name: "checkout",
        path: "/finalizar-compra",
        component: () =>
          import(/* webpackChunkName: "Checkout" */ "@/views/Checkout.vue"),
        meta: {
          checkout: true,
        },
      },
      {
        name: "login",
        path: "/login",
        component: () =>
          import(/* webpackChunkName: "Login" */ "@/views/Login.vue"),
        meta: {
          restricted: true,
        },
      },
      {
        name: "register",
        path: "/cadastre-se",
        component: () =>
          import(/* webpackChunkName: "Register" */ "@/views/Register.vue"),
        meta: {
          restricted: true,
        },
      },
      {
        name: "recovery",
        path: "/recuperar-senha",
        component: () =>
          import(/* webpackChunkName: "Recovery" */ "@/views/Recovery.vue"),
        meta: {
          restricted: true,
        },
      },
      {
        name: "verify",
        path: "/verificar-codigo",
        component: () =>
          import(/* webpackChunkName: "Verify" */ "@/views/Verify.vue"),
        meta: {
          restricted: true,
        },
      },
      {
        name: "reset",
        path: "/nova-senha",
        component: () =>
          import(/* webpackChunkName: "Reset" */ "@/views/Reset.vue"),
        meta: {
          restricted: true,
        },
      },
      {
        name: "profile",
        path: "/minha-conta",
        component: () =>
          import(/* webpackChunkName: "Profile" */ "@/views/Profile.vue"),
        meta: {
          logged: true,
        },
      },
      {
        name: "notFound",
        path: "/404",
        component: () =>
          import(/* webpackChunkName: "NotFound" */ "@/views/NotFound.vue"),
      },
    ],
  },
  // Admin
  {
    name: "admin",
    path: "/admin",
    component: () =>
      import(
        /* webpackChunkName: "PrivateLayout" */ "@/layouts/PrivateLayout.vue"
      ),
    meta: {
      adminRoute: true,
    },
    children: [
      {
        name: "adminContestList",
        path: "/admin/sorteios",
        component: () =>
          import(
            /* webpackChunkName: "AdminContestList" */ "@/views/admin/ContestList.vue"
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
            /* webpackChunkName: "AdminContestCreate" */ "@/views/admin/ContestCreate.vue"
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
            /* webpackChunkName: "AdminContestDetail" */ "@/views/admin/ContestDetail.vue"
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
            /* webpackChunkName: "AdminContestManage" */ "@/views/admin/ContestManage.vue"
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
            /* webpackChunkName: "AdminContestOrders" */ "@/views/admin/ContestOrders.vue"
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
            /* webpackChunkName: "AdminCustomerList" */ "@/views/admin/CustomerList.vue"
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
            /* webpackChunkName: "AdminCustomerDetail" */ "@/views/admin/CustomerDetail.vue"
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
            /* webpackChunkName: "AdminBankAccountList" */ "@/views/admin/BankAccountList.vue"
          ),
        meta: {
          adminRoute: true,
        },
      },
      {
        name: "adminAccountManage",
        path: "/admin/minha-conta",
        component: () =>
          import(/* webpackChunkName: "Profile" */ "@/views/Profile.vue"),
        meta: {
          adminRoute: true,
        },
      },
    ],
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes: routes,
});

router.beforeEach((to, from, next) => {
  const { authenticated, admin } = useAuthStore();
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
