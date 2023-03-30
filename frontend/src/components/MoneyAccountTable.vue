<template>
  <v-card>
    <v-card-text>
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
          <tr v-for="account in accounts" :key="account.id">
            <td>{{ account.name }}</td>
            <td v-bind:class="{'income':account.balance>0,'expense':account.balance<0}">
              {{ formatPrice(account.balance) }}
            </td>
            <td class="expecting">{{ formatPrice(account.expected_income) }}</td>
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
  name: "MoneyAccountTable",
  props: ['accounts'],
  data() {
    return {
      formatPrice: formatPrice
    }
  }
}
</script>

<style scoped>

</style>