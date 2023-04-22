import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({
    state: {
        jwt: localStorage.getItem('access_token'),

        attachedCustomer: undefined,
        detachedCustomer: undefined,
    },
    getters: {
        jwt: state => state.jwt,
    },
    mutations: {
        setToken(state, token) {
            state.jwt = token;
            localStorage.setItem('access_token', token)
        },

        attachToNewsletter(state, user) {
            state.attachedCustomer = user;
        },
        detachFromNewsletter(state, user) {
            state.detachedCustomer = user;
        },
    },
    actions: {},
    modules: {}
})
