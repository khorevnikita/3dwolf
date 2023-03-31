<template>
  <v-row class="mx-3 mt-5">
    <v-col cols="12">
      <div class="d-flex align-items-center">
        <div class="text-h6">Наряд-заказы</div>
        <v-spacer/>
        <v-btn small @click="create()" color="primary">Создать</v-btn>
      </div>
    </v-col>
    <v-col cols="12">
      <v-card>
        <v-card-title>Фильтр</v-card-title>
        <v-card-text>
          <v-row>
            <v-col cols="12" md="3">
              <v-text-field
                  label="Поиск"
                  hide-details
                  v-model="query.search"
                  dense
              />
            </v-col>
            <v-col cols="12" md="3">
              <CustomerPicker
                  v-model="query.customer_id"
                  :dense="true"
              />
            </v-col>
            <v-col cols="12" md="3">
              <v-select
                  label="Статус"
                  v-model="query.status"
                  dense
                  hide-details
                  :items="[
                      {value:'',text:'Все'},
                      ...orderStatusesFilter
                      ]"
                  item-value="value"
                  item-text="text"
              />
            </v-col>
            <v-col cols="12" md="3">
              <v-select
                  label="Платёж"
                  v-model="query.payment_status"
                  dense
                  hide-details
                  :items="[
                      {value:'',text:'Все'},
                      ...paymentStatusesFilter
                      ]"
                  item-value="value"
                  item-text="text"
              />
            </v-col>
          </v-row>
        </v-card-text>
        <v-card-actions>
          <v-btn color="primary" @click="replaceRoute">Найти</v-btn>
        </v-card-actions>
      </v-card>
    </v-col>
    <v-col cols="12">
      <v-data-table
          :headers="headers"
          :items="items"
          :options.sync="options"
          :server-items-length="totalItems"
          :loading="loading"
          class="elevation-1 mt-3"
      >
        <template v-slot:[`item.date`]="{item}">
          {{ item.date ? moment(item.date).format("DD.MM.YYYY") : "-" }}
        </template>
        <template v-slot:[`item.customer_id`]="{item}">
          {{ item.customer ? item.customer.title : '-' }}
        </template>
        <template v-slot:[`item.deadline`]="{item}">
          {{ item.deadline ? moment(item.deadline).format("DD.MM.YYYY") : "-" }}
        </template>
        <template v-slot:[`item.status`]="{item}">
          {{ statuses[item.status] }}
        </template>
        <template v-slot:[`item.payment_status`]="{item}">
          {{ paymentStatuses[item.payment_status] }}
        </template>
        <template v-slot:[`item.amount`]="{item}">
          {{ formatPrice(item.amount) }}
        </template>
        <template v-slot:[`item.actions`]="{item}">
          <v-btn color="primary" icon :to="`/orders/${item.id}`">
            <v-icon>mdi-eye</v-icon>
          </v-btn>
          <v-btn color="warning" icon @click="edit(item)">
            <v-icon>mdi-pencil</v-icon>
          </v-btn>

          <v-btn color="error" icon @click="destroy(item)">
            <v-icon>mdi-delete</v-icon>
          </v-btn>
        </template>
      </v-data-table>
    </v-col>

    <v-dialog v-model="editDialog" max-width="700">
      <OrderEditor
          v-if="editDialog"
          @close="editDialog=false"
          v-model="editItem"
          @created="onCreated"
          @updated="onUpdated"
          :modal="true"
      />
    </v-dialog>
  </v-row>
</template>

<script>

import ResourceComponentHelper from "@/mixins/ResourceComponentHelper";
import OrderEditor from "@/components/Order/OrderEditor";
import CustomerPicker from "@/components/Forms/CustomerPicker";

export default {
  name: "OrdersView",
  components: {CustomerPicker, OrderEditor},
  mixins: [ResourceComponentHelper],
  data() {
    return {
      headers: [
        {text: "ID", value: "id", sortable: false},
        {text: "Дата", value: "date", sortable: false},
        {text: "Клиент", value: "customer_id", sortable: false},
        {text: "Телефон", value: "phone", sortable: false},
        {text: "Сумма", value: "amount", sortable: false},
        {text: "Дедлайн", value: "deadline", sortable: false},
        {text: "Статус", value: "status", sortable: false},
        {text: "Платёж", value: "payment_status", sortable: false},
        {text: "Адрес доставки", value: "delivery_address", sortable: false},
        {text: "", value: "actions", sortable: false},
      ],
      resourceKey: "orders",
      resourceApiParams: "status_sort=1",
      resourceApiRoute: `orders`,
      deleteSwalTitle: `Безвозвратно удалить заказ?`,
      statuses: {
        new: "Новый",
        printing: "В печати",
        shipping: "К отгрузке",
        completed: "Отгружено",
        canceled: "Отказ",
      },
      paymentStatuses: {
        not_paid: "Не оплачено",
        part_paid: "Частично оплачено",
        full_paid: "Полносью оплачено",
      }
    }
  },
  computed: {
    orderStatusesFilter() {
      return Object.keys(this.statuses).map(key => {
        return {value: key, text: this.statuses[key]};
      })
    },
    paymentStatusesFilter() {
      return Object.keys(this.paymentStatuses).map(key => {
        return {value: key, text: this.paymentStatuses[key]};
      })
    },

  }
}
</script>

<style scoped>

</style>