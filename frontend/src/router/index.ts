import Vue from 'vue'
import VueRouter, {RouteConfig} from 'vue-router'
import HomeView from '../views/HomeView.vue'
import store from "@/store";
import axios from "@/plugins/axios";

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
        path: '/masks',
        name: 'masks',
        component: () => import('../views/ProdNumberMasks.vue'),
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
        path: '/newsletters/create',
        name: 'newsletterCreate',
        component: () => import('../views/NewsletterEditor.vue')
    },
    {
        path: '/newsletters/:id',
        name: 'newsletter',
        component: () => import('../views/NewsletterItem.vue')
    },
    {
        path: '/newsletters/:id/edit',
        name: 'newsletterEdit',
        component: () => import('../views/NewsletterEditor.vue')
    },
    {
        path: '/order-notification-templates',
        name: 'order-templates',
        component: () => import('../views/OrderNotificationTemplates.vue')
    },
    {
        path: '/404',
        name: '404',
        component: () => import('../views/404.vue'),
    },
    {
        path: '/profile',
        name: 'profile',
        component: () => import('../views/ProfileView.vue'),
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

    console.log(to.path, apiToken, guestRoute)

    if (guestRoute && !apiToken) {
        next();
    } else if (guestRoute && apiToken) {
        next({path: "/"})
    } else if (!guestRoute && !apiToken) {
        next({path: "/login"})
    } else {
        let user = store.getters.user;
        if (!user) {
            const body = <any>await axios.get(`auth/me`);
            user = body.user;
            store.commit('setUser', user)
        }

        const route = store.getters.routes.find((r: any) => r.to.split('/')[1] === to.path.split('/')[1]);

        if (!route?.permission) {
            next()
        } else {
            if (user.permission.includes(route.permission)) {
                next()
            } else {
                next({path: "/"});
            }
        }
    }
});

export default router
