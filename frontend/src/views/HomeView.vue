<template>
  <v-row class="mx-3 mt-5 dashboard">
    <v-col cols="12" v-if="!isModerator && customer">
      <v-card>
        <v-card-subtitle>Баланс</v-card-subtitle>
        <v-card-title>
          <div class="text-h3">{{ formatPrice(customer.balance) }}</div>
        </v-card-title>
      </v-card>
    </v-col>
    <v-col cols="12" v-if="showCustomers">
      <div class="text-h4">Клиенты</div>
    </v-col>
    <v-col cols="6" v-if="showCustomers">
      <v-card>
        <v-card-subtitle>Всего клиентов</v-card-subtitle>
        <v-card-title>
          <div class="text-h3" v-if="data.customers">{{ data.customers?.total }}</div>
        </v-card-title>
      </v-card>
    </v-col>
    <v-col cols="6" v-if="showCustomers">
      <v-card>
        <v-card-subtitle>Новых клиентов</v-card-subtitle>
        <v-card-title>
          <div class="text-h3" v-if="data.customers">{{ data.customers?.new }}</div>
        </v-card-title>
      </v-card>
    </v-col>
    <v-col cols="6" v-if="showCustomers">
      <v-card>
        <v-card-subtitle>Авито</v-card-subtitle>
        <v-card-title>
          <div class="text-h3">{{ data.sources?.avito }}</div>
        </v-card-title>
      </v-card>
    </v-col>
    <v-col cols="6" v-if="showCustomers">
      <v-card>
        <v-card-subtitle>Сайт</v-card-subtitle>
        <v-card-title>
          <div class="text-h3">{{ data.sources?.site }}</div>
        </v-card-title>
      </v-card>
    </v-col>
    <v-col cols="12">
      <div class="text-h4">Заказы</div>
    </v-col>
    <v-col cols="6" md="3" lg="2" v-for="(statusKey,i) in Object.keys(statuses)">
      <v-card>
        <v-card-subtitle>{{ statuses[statusKey] }}</v-card-subtitle>
        <v-card-title>
          <div class="text-h3" v-if="data.orders">{{ data.orders[statusKey] ? data.orders[statusKey] : 0 }}</div>
        </v-card-title>
      </v-card>
    </v-col>
    <v-col cols="12" v-if="showMoney">
      <div class="text-h4">Деньги</div>
    </v-col>
    <v-col cols="12" md="6" v-if="showMoney">
      <MonthMoneyTable :months="data.months"/>
    </v-col>
    <v-col cols="12" md="6" v-if="showMoney">
      <MoneyAccountTable :accounts="data.accounts"/>
    </v-col>
    <v-col cols="12" v-if="showStock">
      <div class="text-h4">Склад</div>
    </v-col>
    <v-col cols="12" v-if="showStock">
      <StockStats :stock="data.stock"/>
    </v-col>
  </v-row>
</template>

<script>
import Vue from 'vue'
import MonthMoneyTable from "@/components/Dashboard/MonthMoneyTable.vue";
import MoneyAccountTable from "@/components/Dashboard/MoneyAccountTable.vue";
import StockStats from "@/components/Dashboard/StockStats.vue";
import axios from "@/plugins/axios";
import {mapGetters} from "vuex";
import {formatPrice} from "@/plugins/formats";

export default Vue.extend({
  name: 'Home',
  components: {MoneyAccountTable, MonthMoneyTable, StockStats},
  data() {
    return {
      statuses: {
        new: "Новый",
        printing: "В печати",
        moving: "Перемещение на ПВЗ",
        moving_tk: "Перемещение ТК",
        shipping: "Готов к отгрузке",
        completed: "Отгружено",
      },
      data: {},
      formatPrice: formatPrice,
    }
  },
  created() {
    this.getDashboardData();
  },
  computed: {
    ...mapGetters(['user', 'isModerator']),
    customer() {
      return this.user?.customer;
    },
    showMoney() {
      return this.user && this.user.permission?.includes('payments');
    },
    showCustomers() {
      return this.user && this.user.permission?.includes('customers');
    },
    showStock() {
      return this.user && this.user.permission?.includes('parts');
    },
  },
  methods: {
    getDashboardData() {
      axios.get(`money/dashboard`).then(body => {
        this.data = body.data;
      })
    }
  }
})
</script>
<style>
.dashboard .v-card__subtitle {
  padding-bottom: 0;
}
</style>