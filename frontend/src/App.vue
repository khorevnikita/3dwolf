<template>
  <v-app>
    <v-navigation-drawer app v-if="jwt" v-model="drawer">
      <v-list-item>
        <v-list-item-content>
          <v-list-item-title class="text-h6">
            <v-img contain position="left" max-height="50" alt="logo" :src="require('@/assets/logo.svg')"/>
          </v-list-item-title>
          <v-list-item-subtitle>
            3D WOLF | CRM
          </v-list-item-subtitle>
        </v-list-item-content>
      </v-list-item>

      <v-divider></v-divider>

      <v-list dense nav>
        <v-list-item
            v-for="item in visibleRoutes"
            :key="item.title"
            link
            :to="item.to"
        >
          <v-list-item-icon>
            <v-icon>{{ item.icon }}</v-icon>
          </v-list-item-icon>

          <v-list-item-content>
            <v-list-item-title>{{ item.title }}</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>
    <v-main>
      <v-container fluid>
        <router-view/>
      </v-container>
    </v-main>
    <v-bottom-navigation app shift v-if="isMobile">
      <v-btn to="/money" icon v-if="show('payments')">
        <span>Деньги</span>
        <v-icon>mdi-cash</v-icon>
      </v-btn>
      <v-btn to="/orders" icon v-if="show('orders')">
        <span>Заказы</span>

        <v-icon>mdi-cart-outline</v-icon>
      </v-btn>

      <v-btn to="/" icon>
        <span>Главная</span>

        <v-icon>mdi-view-dashboard</v-icon>
      </v-btn>

      <v-btn to="/customers" icon v-if="show('customers')">
        <span>Клиенты</span>

        <v-icon>mdi-toolbox</v-icon>
      </v-btn>
      <v-btn @click="drawer=!drawer" icon>
        <span>Меню</span>

        <v-icon>mdi-menu</v-icon>
      </v-btn>

    </v-bottom-navigation>
  </v-app>
</template>

<script>
import Vue from 'vue';
import {mapGetters} from "vuex";

export default Vue.extend({
  name: 'App',

  data() {
    return {
      bottom: null,
      drawer: !this.$vuetify.breakpoint.mobile,
    }
  },
  computed: {
    ...mapGetters(['jwt', 'routes', 'user','visibleRoutes']),
    isMobile() {
      return this.$vuetify.breakpoint.mobile;
    },
  },
  methods: {
    show(key) {
      return this.user && this.user.permission?.includes(key);
    }
  }
});
</script>
<style>
.income {
  background: #dfffae;
}

.expense {
  background: #ffaed0;
}

.expecting {
  background: #ebebeb;
}

.swal2-styled.swal2-confirm {
  background-color: #0D6B78 !important;
}

.swal2-styled.swal2-confirm:focus {
  box-shadow: none !important;
}
</style>