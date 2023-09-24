import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({
    state: {
        routes: [
            {title: 'Главная', icon: 'mdi-view-dashboard', to: '/',},
            {title: 'Клиенты', icon: 'mdi-toolbox', to: '/customers', permission: 'customers'},

            {title: 'Наряд-заказы', icon: 'mdi-cart-outline', to: '/orders', permission: 'orders'},
            {title: 'Деньги', icon: 'mdi-cash', to: '/money', permission: 'payments'},
            {title: 'Рег. платежи', icon: 'mdi-cash-clock', to: '/regular-payments', permission: 'regular_payments'},
            {title: 'Задачи', icon: 'mdi-calendar-check', to: '/tasks', permission: 'tasks'},
            {title: 'Склад', icon: 'mdi-store', to: '/stock', permission: 'parts'},
            {title: 'Филиалы', icon: 'mdi-source-branch', to: '/branches', permission: 'branches'},
            {
                title: "Справочники", icon: "mdi-palette-swatch-variant", children: [
                    {
                        title: 'Адреса доставки',
                        icon: 'mdi-truck-delivery-outline',
                        to: '/delivery-addresses',
                        permission: 'delivery_address'
                    },
                    {title: 'Материалы', icon: 'mdi-box-cutter', to: '/materials', permission: 'materials'},
                    {
                        title: 'Производители',
                        icon: 'mdi-office-building-cog',
                        to: '/manufacturers',
                        permission: 'manufacturers'
                    },
                    {title: 'Счета', icon: 'mdi-wallet-plus', to: '/accounts', permission: 'accounts'},
                    {title: 'Сотрудники', icon: 'mdi-account-multiple', to: '/users', permission: 'users'},
                    {title: 'Цели платежей', icon: 'mdi-credit-card-settings-outline', to: '/payment-purposes', permission: 'payment_purpose'},
                ],
            },
            {
                title: "Шаблоны", icon: "mdi-clipboard-list-outline", children: [
                    {title: 'Договора', icon: 'mdi-file-sign', to: '/contracts', permission: 'contracts'},
                    {title: 'Сметы', icon: 'mdi-clipboard-list', to: '/estimates', permission: 'estimates'},
                ]
            },
            {title: 'Рассылки', icon: 'mdi-email-arrow-right-outline', to: '/newsletters', permission: 'newsletters'},
            {title: 'Профиль', icon: 'mdi-account', to: '/profile'},
            {title: 'Настройки', icon: 'mdi-cog', to: '/settings', permission: "settings"},
        ],

        jwt: localStorage.getItem('access_token'),
        user: undefined as any,
        attachedCustomer: undefined,
        detachedCustomer: undefined,

        accessLevels: [
            {key: 0, value: "Клиент"},
            {key: 1, value: "Сотрудник"},
            {key: 2, value: "Администратор"},
        ],
        permissionTypes: [
            {title: 'Сотрудники', type: 'users'},
            {title: 'Филиалы', type: 'branches'},
            {title: 'Клиенты', type: 'customers'},
            {title: 'Материалы', type: 'materials'},
            {title: 'Производители', type: 'manufacturers'},
            {title: 'Склад', type: 'parts'},
            {title: 'Счета', type: 'accounts'},
            {title: 'Наряд-заказы', type: 'orders'},
            {title: 'Адреса доставки', type: 'delivery_address'},
            {title: 'Договора', type: 'contracts'},
            {title: 'Деньги', type: 'payments'},
            {title: 'Цели платежей', type: 'payment_purpose'},
            {title: 'Рег. платежи', type: 'regular_payments'},
            {title: 'Сметы', type: 'estimates'},
            {title: 'Рассылки', type: 'newsletters'},
            {title: 'Задачи', type: 'tasks'},
            {title: 'Настройки', type: 'settings'},
        ],
    },
    getters: {
        jwt: state => state.jwt,
        user: state => state.user,
        routes: state => state.routes,
        visibleRoutes: state => {
            const user = <any>state.user;
            if (!user) return [];
            if (!user.permission) return [];
            return state.routes
        },
        isModerator: state => {
            return state.user && state.user.access_level > 0;
        },
        accessLevels: state => state.accessLevels,
        permissionTypes: state => state.permissionTypes,
    },
    mutations: {
        setToken(state, token) {
            state.jwt = token;
            localStorage.setItem('access_token', token)
        },
        logout(state) {
            state.jwt = null;
            localStorage.removeItem('access_token')
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
    actions: {
        LOGOUT({state, commit}) {
            commit('logout')
        }
    },
    modules: {},
})
