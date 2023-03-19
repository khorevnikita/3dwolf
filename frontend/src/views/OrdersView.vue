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
            <v-col cols="12" md="4">
              <v-text-field
                  label="Поиск"
                  hide-details
                  v-model="query.search"
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
          {{ statusMap[item.status] }}
        </template>
        <template v-slot:[`item.payment_status`]="{item}">
          {{ statusMap[item.payment_status] }}
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

export default {
  name: "OrdersView",
  components: {OrderEditor},
  mixins: [ResourceComponentHelper],
  data() {
    return {
      headers: [
        {text: "ID", value: "id", sortable: true},
        {text: "Дата", value: "date", sortable: true},
        {text: "Клиент", value: "customer_id", sortable: false},
        {text: "Телефон", value: "phone", sortable: false},
        {text: "Сумма", value: "amount", sortable: true},
        {text: "Дедлайн", value: "deadline", sortable: true},
        {text: "Статус", value: "status", sortable: true},
        {text: "Платёж", value: "payment_status", sortable: true},
        {text: "Адрес доставки", value: "delivery_address", sortable: false},
        {text: "", value: "actions", sortable: false},
      ],
      resourceKey: "orders",
      resourceApiRoute: `orders`,
      deleteSwalTitle: `Безвозвратно удалить заказ?`,
      statusMap: {
        new: "Новый",
        printing: "В печати",
        shipping: "К откгрузке",
        complete: "Отгружено",
        not_paid: "Не оплачено",
        part_paid: "Частично оплачено",
        full_paid: "Полносью оплачено",
      }
    }
  },
}
</script>

<style scoped>

</style>