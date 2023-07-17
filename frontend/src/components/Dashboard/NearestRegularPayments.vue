<template>
  <v-card>
    <v-card-title class="text--error error--text">
      Ближайшие регулярные платежи
    </v-card-title>
    <v-card-text>
      <v-simple-table>
        <template v-slot:default>
          <thead>
          <tr>
            <th class="text-left">
              Дата
            </th>
            <th class="text-left">
              Кому
            </th>
            <th class="text-left">
              Сумма
            </th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="payment in regularPayments" :key="payment.id">
            <td>{{ moment(payment.next_date).format("DD.MM.YYYY") }}</td>
            <td>{{ payment.recipient }}</td>
            <td>{{ formatPrice(payment.amount) }}</td>
          </tr>
          </tbody>
        </template>
      </v-simple-table>
    </v-card-text>
  </v-card>
</template>

<script>
import {formatPrice} from "@/plugins/formats";
import moment from "moment";

export default {
  name: "NearestRegularPayments",
  props: ['regularPayments'],
  data() {
    return {
      formatPrice: formatPrice,
      moment: moment
    }
  },
  created() {

  }
}
</script>

<style scoped>

</style>