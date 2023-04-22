import Vue from 'vue'
import VueRouter, {RouteConfig} from 'vue-router'
import HomeView from '../views/HomeView.vue'
import store from "@/store";

Vue.use(VueRouter)

const routes: Array<RouteConfig> = [
    {
        path: '/',
        name: 'home',
        component: HomeView
    },
    {
        path: '/login',
        name: 'login',
        component: () => import('../views/LoginView.vue'),
        meta: {
            guest: true
        }
    },
    {
        path: '/users',
        name: 'users',
        component: () => import('../views/UsersView.vue'),
    },
    {
        path: '/customers',
        name: 'customers',
        component: () => import('../views/CustomersView.vue'),
    },
    {
        path: '/customers/:id',
        name: 'customer',
        component: () => import('../views/CustomerItem.vue'),
    },

    {
        path: '/materials',
        name: 'materials',
        component: () => import('../views/MaterialsView.vue'),
    },
    {
        path: '/manufacturers',
        name: 'manufacturers',
        component: () => import('../views/ManufacturersView.vue'),
    },
    {
        path: '/stock',
        name: 'stock',
        component: () => import('../views/StockView.vue'),
    },
    {
        path: '/accounts',
        name: 'accounts',
        component: () => import('../views/AccountsView.vue'),
    },
    {
        path: '/orders',
        name: 'orders',
        component: () => import('../views/OrdersView.vue'),
    },
    {
        path: '/orders/:id',
        name: 'order',
        component: () => import('../views/OrderItem.vue'),
    },
    {
        path: '/contracts',
        name: 'contracts',
        component: () => import('../views/ContractsView.vue'),
    },
    {
        path: '/estimates',
        name: 'estimates',
        component: () => import('../views/EstimatesView.vue'),
    },
    {
        path: '/estimates/:id',
        name: 'estimate',
        component: () => import('../views/EstimateItem.vue'),
    },
    {
        path: '/money',
        name: 'money',
        component: () => import('../views/MoneyView.vue'),
    },
    {
        path: '/newsletters',
        name: 'newsletters',
        component: () => import('../views/NewslettersView.vue')
    },
    {
        path: '/newsletters/:id',
        name: 'newsletter',
        component: () => import('../views/NewsletterItem.vue')
    },
    {
        path: '/404',
        name: '404',
        component: () => import('../views/404.vue'),
    },
]

const router = new VueRouter({
    mode: 'history',
    base: process.env.BASE_URL,
    routes
})

router.beforeEach(async (to, from, next) => {
    if (to === from) {
        return;
    }

    if (to.matched.length === 0) {
        next({path: '/404'})
    }

    const apiToken = !!store.getters.jwt;
    const guestRoute = to.matched.some(record => record.meta.guest);
    if (guestRoute && apiToken) {
        next({path: "/"})
    } else if (!guestRoute && !apiToken) {
        next({path: "/login"})
    } else {
        next()
    }
});

export default router
