<template>
  <v-card>
    <v-card-text>
      <v-list>
        <v-list-item>
          <v-list-item-subtitle>Дата</v-list-item-subtitle>
          <v-list-item-title>{{ moment(order.date).format("DD.MM.YYYY") }}</v-list-item-title>
        </v-list-item>
        <v-list-item>
          <v-list-item-subtitle>Дедлайн</v-list-item-subtitle>
          <v-list-item-title>{{ moment(order.deadline).format("DD.MM.YYYY") }}</v-list-item-title>
        </v-list-item>
        <v-list-item>
          <v-list-item-subtitle>Статус</v-list-item-subtitle>
          <v-list-item-title>{{ orderStatuses[order.status] }}</v-list-item-title>
        </v-list-item>
        <v-list-item>
          <v-list-item-subtitle>Статус оплаты</v-list-item-subtitle>
          <v-list-item-title>{{ paymentStatuses[order.payment_status] }}</v-list-item-title>
        </v-list-item>
        <v-list-item>
          <v-list-item-subtitle>Адрес доставки</v-list-item-subtitle>
          <v-list-item-title>{{ order.delivery_address }}</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-card-text>
  </v-card>
</template>

<script>
import moment from "moment";

export default {
  name: "OrderViewer",
  props: ['order'],
  data() {
    return {
      moment: moment,
      orderStatuses: {
        new: 'Новый',
        printing: 'В печати',
        moving: 'Перемещение на ПВЗ',
        moving_tk: 'Перемещение ТК',
        shipping: 'Готов к отгрузке',
        completed: 'Отгружен',
        canceled: 'Отменён',
      },
      paymentStatuses: {
        not_paid: 'Не оплачен',
        part_paid: 'Частично оплачен',
        full_paid: 'Полностью оплачен',
      }
    }
  }
}
</script>

<style scoped>

</style>