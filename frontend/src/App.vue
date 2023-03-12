<template>
  <v-app>
    <v-navigation-drawer app permanent v-if="jwt">
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
            v-for="item in items"
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
  </v-app>
</template>

<script lang="ts">
import Vue from 'vue';
import {mapGetters} from "vuex";

export default Vue.extend({
  name: 'App',

  data: () => ({
    items: [
      {title: 'Главная', icon: 'mdi-view-dashboard', to: '/'},
      {title: 'Сотрудники', icon: 'mdi-account-multiple', to: '/users'},
      {title: 'Клиенты', icon: 'mdi-toolbox', to: '/customers'},
      {title: 'Материалы', icon: 'mdi-box-cutter', to: '/materials'},
      {title: 'Производители', icon: 'mdi-office-building-cog', to: '/manufacturers'},
      {title: 'Склад', icon: 'mdi-store', to: '/stock'},
      {title: 'Счета', icon: 'mdi-wallet-plus', to: '/accounts'},
      {title: 'Наряд-заказы', icon: 'mdi-cart-outline', to: '/orders'},
      {title: 'Договора', icon: 'mdi-file-sign', to: '/contracts'},
      {title: 'Сметы', icon: 'mdi-clipboard-list', to: '/estimates'},
    ],
  }),
  computed: {
    ...mapGetters(['jwt'])
  }
});
</script>
