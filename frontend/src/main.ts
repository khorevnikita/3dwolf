import Vue from 'vue'
import App from './App.vue'
import './registerServiceWorker'
import router from './router'
import store from './store'
import vuetify from './plugins/vuetify';
// @ts-ignore
import QueryHelper from "./mixins/QueryHelper.js";
import VueMask from 'v-mask';

// @ts-ignore
Vue.use(VueMask)
Vue.config.productionTip = false
Vue.mixin(QueryHelper);
Vue.mixin({
  methods: {
    copyObject(object) {
      return JSON.parse(JSON.stringify(object))
    }
  }
})
new Vue({
  router,
  store,
  vuetify,
  render: h => h(App)
}).$mount('#app')
