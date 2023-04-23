import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({
    state: {
        routes: [
            {title: 'Главная', icon: 'mdi-view-dashboard', to: '/',},
            {title: 'Сотрудники', icon: 'mdi-account-multiple', to: '/users', permission: 'users'},
            {title: 'Клиенты', icon: 'mdi-toolbox', to: '/customers', permission: 'customers'},
            {title: 'Материалы', icon: 'mdi-box-cutter', to: '/materials', permission: 'materials'},
            {
                title: 'Производители',
                icon: 'mdi-office-building-cog',
                to: '/manufacturers',
                permission: 'manufacturers'
            },
            {title: 'Склад', icon: 'mdi-store', to: '/stock', permission: 'parts'},
            {title: 'Счета', icon: 'mdi-wallet-plus', to: '/accounts', permission: 'accounts'},
            {title: 'Наряд-заказы', icon: 'mdi-cart-outline', to: '/orders', permission: 'orders'},
            {title: 'Договора', icon: 'mdi-file-sign', to: '/contracts', permission: 'contracts'},
            {title: 'Деньги', icon: 'mdi-cash', to: '/money', permission: 'payments'},
            {title: 'Сметы', icon: 'mdi-clipboard-list', to: '/estimates', permission: 'estimates'},
            {title: 'Рассылки', icon: 'mdi-email-arrow-right-outline', to: '/newsletters', permission: 'newsletters'},
        ],

        jwt: localStorage.getItem('access_token'),
        user: undefined,
        attachedCustomer: undefined,
        detachedCustomer: undefined,
    },
    getters: {
        jwt: state => state.jwt,
        user: state => state.user,
        routes: state => state.routes,
        visibleRoutes: state => {
            const user = <any>state.user;
            if (!user) return [];
            if (!user.permission) return [];
            return state.routes.filter((item) => {
                if (!item.permission) return true;
                return user.permission.includes(item.permission)
            })
        }
    },
    mutations: {
        setToken(state, token) {
            state.jwt = token;
            localStorage.setItem('access_token', token)
        },
        setUser(state, user) {
            state.user = user;
        },
        attachToNewsletter(state, user) {
            state.attachedCustomer = user;
        },
        detachFromNewsletter(state, user) {
            state.detachedCustomer = user;
        },
    },
    actions: {},
    modules: {},
})
