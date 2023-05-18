<template>
  <v-card>
    <v-card-title>Клиент</v-card-title>
    <v-card-text>
      <v-list>
        <v-list-item>
          <v-list-item-content>
            <v-list-item-subtitle>Название</v-list-item-subtitle>
            <v-list-item-title>{{ customer.title }}</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-list-item>
          <v-list-item-content>
            <v-list-item-subtitle>Имя</v-list-item-subtitle>
            <v-list-item-title>
              {{ [customer.surname, customer.name, customer.father_name].join(" ") }}
            </v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-list-item>
          <v-list-item-content>
            <v-list-item-subtitle>Контакт</v-list-item-subtitle>
            <v-list-item-title>{{ customer.email }} {{ customer.phone }}</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-list-item>
          <v-list-item-content>
            <v-list-item-subtitle>Заказы (мес/всего)</v-list-item-subtitle>
            <v-list-item-title>{{ customer.recent_orders_count }} / {{ customer.orders_count }}</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-list-item>
          <v-list-item-content>
            <v-list-item-subtitle>Сумма (мес/всего)</v-list-item-subtitle>
            <v-list-item-title>
              {{ round(customer.recent_payments_sum_amount) }}
              /
              {{ round(customer.payments_sum_amount) }}
            </v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-list-item>
          <v-list-item-content>
            <v-list-item-subtitle>Дата последнего заказа</v-list-item-subtitle>
            <v-list-item-title>
              {{ customer.last_paid_payment ? moment(customer.last_paid_payment.paid_at).format("DD.MM.YYYY") : '-' }}
            </v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-card-text>
    <v-card-actions>
      <v-spacer/>
      <v-btn text :to="`/customers/${customer.id}`">Открыть профиль</v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
import moment from "moment";

export default {
  name: "CustomerCard",
  props: ['customer'],
  data() {
    return {
      moment: moment,
    }
  },
  methods: {
    round(num) {
      return Math.round(num * 100) / 100;
    }
  }
}
</script>

<style scoped>

</style>