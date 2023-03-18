<template>
  <div>
    <v-select
        label="Год"
        :items="[2023]"
        v-model="year"
    />
    <v-row>
      <v-col cols="12" md="6">
        <v-card>
          <v-card-subtitle>
            Оборотка
          </v-card-subtitle>
          <v-card-title>
            {{ data.income }}
          </v-card-title>
        </v-card>
      </v-col>
      <v-col cols="12" md="6">
        <v-card>
          <v-card-subtitle>
            Остаток
          </v-card-subtitle>
          <v-card-title>
            {{ data.balance }}
          </v-card-title>
        </v-card>
      </v-col>

      <v-col cols="12" md="6">

        <v-simple-table>
          <template v-slot:default>
            <thead>
            <tr>
              <th class="text-left">
                Месяц
              </th>
              <th class="text-left">
                Доход
              </th>
              <th class="text-left">
                Расход
              </th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(monthData,index) in data.months" :key="index">
              <td>{{ months[monthData.month - 1] }}</td>
              <td>{{ monthData.income }}</td>
              <td>{{ monthData.expense }}</td>
            </tr>
            </tbody>
          </template>
        </v-simple-table>
      </v-col>
      <v-col cols="12" md="6">
        <v-simple-table>
          <template v-slot:default>
            <thead>
            <tr>
              <th class="text-left">
                Счёт
              </th>
              <th class="text-left">
                Факт
              </th>
              <th class="text-left">
                Ожидание
              </th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="account in data.accounts" :key="account.id">
              <td>{{ account.name }}</td>
              <td>{{ account.balance }}</td>
              <td>{{ account.expected_income }}</td>
            </tr>
            </tbody>
          </template>
        </v-simple-table>
      </v-col>
    </v-row>
  </div>
</template>

<script>
import axios from "@/plugins/axios";

export default {
  name: "MoneyView",
  data() {
    return {
      year: 2023,
      months: [
        'Январь',
        'Февраль',
        'Март',
        'Апрель',
        'Май',
        'Июнь',
        'Июль',
        'Август',
        'Сентябрь',
        'Октябрь',
        'Ноябрь',
        'Декабрь',
      ],
      data: {}
    }
  },
  created() {
    this.getData();
  },
  watch: {
    year() {
      this.getData();
    }
  },
  methods: {
    getData() {
      axios.get(`money/statistics?year=${this.year}`).then(body => {
        this.data = body.data;
      })
    }
  }
}
</script>

<style scoped>

</style>