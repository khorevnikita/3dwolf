<template>
  <v-card>
    <v-card-text>
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
          <tr v-for="(monthData,index) in months" :key="index">
            <td>{{ monthsMap[monthData.month - 1] }}</td>
            <td class="income">{{ formatPrice(monthData.income) }}</td>
            <td class="expense">{{ formatPrice(monthData.expense) }}</td>
          </tr>
          </tbody>
        </template>
      </v-simple-table>
    </v-card-text>
  </v-card>
</template>

<script>
import {formatPrice} from "@/plugins/formats";

export default {
  name: "MonthMoneyTable",
  props: ['months'],
  data(){
    return{
      formatPrice: formatPrice,
      monthsMap: [
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
    }
  }
}
</script>

<style scoped>

</style>