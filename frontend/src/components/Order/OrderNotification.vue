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
  </v-row>
</template>

<script>
import axios from "@/plugins/axios";
import Swal from "sweetalert2-khonik";

export default {
  name: "OrderNotification",
  props: ['order'],
  data() {
    return {
      attach: false,
    }
  },
  methods: {
    notify(channel) {
      axios.post(`orders/${this.order.id}/notify`, {
        attach: this.attach,
        channel: channel,
      }).then(()=>{
        Swal.fire("Отправлено")
      }).catch(()=>{
        Swal.fire("Ошибка при отправке")
      })
    }
  }
}
</script>

<style scoped>

</style>