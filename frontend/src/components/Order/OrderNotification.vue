<template>
  <v-row>
    <v-col cols="6">
      <v-btn color="info" small @click="notify('email')">Уведомить по email</v-btn>
      <v-switch
          v-model="attach"
          label="Прикрепить наряд-заказ PDF"
          dense
          hide-details
      />
    </v-col>
    <v-col cols="6">
      <v-btn color="info" small @click="notify('sms')">Уведомить по SMS</v-btn>
    </v-col>
    <v-col cols="12">
      <v-list>
        <v-list-item v-for="log in logs" :key="log.id">
          <v-list-item-content>
            <v-list-item-title>
              {{ orderStatusLabel(log.order_status) }} по {{ log.channel }}
              <v-icon v-if="log.attached">mdi-file-pdf-box</v-icon>
            </v-list-item-title>
            <v-list-item-subtitle>
              {{ moment(log.created_at).format('HH:mm DD.MM.YYYY') }}
              от
              {{ [log.user.name, log.user.surname].join(" ") }}
            </v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-col>
  </v-row>
</template>

<script>
import axios from "@/plugins/axios";
import Swal from "sweetalert2-khonik";
import {orderStatuses, orderStatusLabel} from "@/mixins/StatusHelper";
import moment from "moment";

export default {
  name: "OrderNotification",
  props: ['order'],
  data() {
    return {
      attach: false,
      logs: [],
      statuses: orderStatuses,
      orderStatusLabel: orderStatusLabel,
      moment: moment,
    }
  },
  created() {
    this.getLogs();
  },
  methods: {
    getLogs() {
      axios.get(`orders/${this.order.id}/notification-logs`).then(({orderNotificationLogs}) => {
        this.logs = orderNotificationLogs;
      })
    },
    notify(channel) {
      axios.post(`orders/${this.order.id}/notify`, {
        attach: this.attach,
        channel: channel,
      }).then(() => {
        this.getLogs();
        Swal.fire("Отправлено")
      }).catch(() => {
        Swal.fire("Ошибка при отправке")
      })
    }
  }
}
</script>

<style scoped>

</style>