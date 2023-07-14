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
    <!--<v-col cols="12">
      <v-chip
          class="ma-2"
          v-for="(status,i) in statuses" :key="i"
      >
        <v-avatar left
                  color="primary" class="white--text">
          {{ data.orders[status.value] ? data.orders[status.value] : 0 }}
        </v-avatar>
        {{ orderStatusLabel(status.value) }}
      </v-chip>
      <v-chip class="ma-2">
        <v-avatar left color="primary" class="white--text">0</v-avatar>
        Отгружено за месяц
      </v-chip>
    </v-col>-->
    <v-col cols="12">
      <v-card>
        <v-card-text>
          <v-simple-table>
            <template v-slot:default>
              <thead>
              <tr>
                <th class="text-left">
                  Статус
                </th>
                <th class="text-left">
                  Кол-во
                </th>
              </tr>
              </thead>
              <tbody>
              <tr>
                <td><b>Отгружено за месяц</b></td>
                <td><b>{{ data.orders.completed_by_month }}</b></td>
              </tr>
              <tr v-for="(status,i) in statuses" :key="i">
                <td>{{ orderStatusLabel(status.value) }}</td>
                <td>{{ data.orders[status.value] ? data.orders[status.value] : 0 }}</td>
              </tr>
              </tbody>
            </template>
          </v-simple-table>
        </v-card-text>
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
      <div class="mt-2" v-if="data.client_credit_amount">Положительный баланс клиентов:
        <b>{{ formatPrice(data.client_credit_amount) }}</b></div>
    </v-col>
    <v-col cols="12" v-if="showStock">
      <div class="text-h4">Склад</div>
    </v-col>
    <v-col cols="12" v-if="showStock">
      <div>Всего: <b>{{ data.parts_count }}</b></div>
      <div>Суммарный вес: <b>{{ formatKg(data.parts_weight) }} кг</b></div>
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
import {formatKg, formatPrice} from "@/plugins/formats";
import {orderStatuses, orderStatusLabel} from "@/mixins/StatusHelper";

export default Vue.extend({
  name: 'Home',
  components: {MoneyAccountTable, MonthMoneyTable, StockStats},
  data() {
    return {
      statuses: orderStatuses,
      orderStatusLabel: orderStatusLabel,
      data: {},
      formatPrice: formatPrice,
      formatKg: formatKg,
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