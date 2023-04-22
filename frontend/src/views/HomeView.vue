<template>
  <v-row class="mx-3 mt-5 dashboard">
    <v-col cols="12">
      <div class="text-h4">Клиенты</div>
    </v-col>
    <v-col cols="6">
      <v-card>
        <v-card-subtitle>Всего клиентов</v-card-subtitle>
        <v-card-title>
          <div class="text-h3" v-if="data.customers">{{ data.customers?.total }}</div>
        </v-card-title>
      </v-card>
    </v-col>
    <v-col cols="6">
      <v-card>
        <v-card-subtitle>Новых клиентов</v-card-subtitle>
        <v-card-title>
          <div class="text-h3" v-if="data.customers">{{ data.customers?.new }}</div>
        </v-card-title>
      </v-card>
    </v-col>
    <v-col cols="6">
      <v-card>
        <v-card-subtitle>Авито</v-card-subtitle>
        <v-card-title>
          <div class="text-h3">{{ data.sources?.avito }}</div>
        </v-card-title>
      </v-card>
    </v-col>
    <v-col cols="6">
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
    <v-col cols="6" md="3" v-for="statusKey in Object.keys(statuses)">
      <v-card>
        <v-card-subtitle>{{ statuses[statusKey] }}</v-card-subtitle>
        <v-card-title>
          <div class="text-h3">{{ data.orders[statusKey] ? data.orders[statusKey] : 0 }}</div>
        </v-card-title>
      </v-card>
    </v-col>
    <v-col cols="12">
      <div class="text-h4">Деньги</div>
    </v-col>
    <v-col cols="12" md="6">
      <MonthMoneyTable :months="data.months"/>
    </v-col>
    <v-col cols="12" md="6">
      <MoneyAccountTable :accounts="data.accounts"/>
    </v-col>
  </v-row>
</template>

<script lang="ts">
import Vue from 'vue'
import MonthMoneyTable from "@/components/MonthMoneyTable.vue";
import MoneyAccountTable from "@/components/MoneyAccountTable.vue";
import axios from "@/plugins/axios";

export default Vue.extend({
  name: 'Home',
  components: {MoneyAccountTable, MonthMoneyTable},
  data() {
    return {
      statuses: {
        new: "Новый",
        printing: "В печати",
        shipping: "К отгрузке",
        completed: "Отгружено",
      },
      data: {}
    }
  },
  created() {
    this.getDashboardData();
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