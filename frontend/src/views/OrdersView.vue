<template>
  <v-row class="mx-3 mt-5">
    <v-col cols="12">
      <div class="d-flex align-items-center">
        <div class="text-h6">Наряд-заказы</div>
        <v-spacer/>
        <v-btn v-if="isModerator" small to="/order-notification-templates" color="secondary" class="mr-4">Шаблоны
          уведомлений
        </v-btn>
        <v-btn v-if="isModerator" small @click="create()" color="primary">Создать</v-btn>
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
              <BranchPicker
                  v-model="query.branch_id"
                  :dense="true"
              />
            </v-col>
            <v-col cols="12" md="3" v-if="isModerator">
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
                      ...statuses
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
                      ...paymentStatuses
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
      <v-simple-table>
        <template v-slot:default>
          <thead>
          <tr>
            <th class="text-left" v-for="(header,i) in headers" :key="i">
              {{ header.text }}
            </th>
          </tr>
          </thead>
          <tbody>
          <tr
              v-for="(item,i) in items"
              :key="i"
              v-bind:class="{
                'info':item.status==='new',
                'success':['modeling','printing'].includes(item.status),
                'warning':item.status==='shipping',
                'moving':['moving_tk','moving','processing'].includes(item.status)
              }"
          >
            <td>{{ item.id }}</td>
            <td>{{ item.date ? moment(item.date).format("DD.MM.YYYY") : "-" }}</td>
            <td>{{ item.customer ? item.customer.title : '-' }}</td>
            <td>{{ item.phone }}</td>
            <td>{{ formatPrice(item.amount) }}</td>
            <td>{{ item.deadline ? moment(item.deadline).format("DD.MM.YYYY") : "-" }}</td>
            <td>
              {{ orderStatusLabel(item.status) }}
              <div v-if="item.branch" style="font-size: 10px">
                <b>{{ item.branch.name }}</b>
              </div>
            </td>
            <td
                v-bind:class="{'error': ['modeling','printing','processing','shipping','moving','moving_tk'].includes(item.status) &&item.payment_status!=='full_paid'}"
            >
              {{ paymentStatusLabel(item.payment_status) }}
            </td>
            <td>{{ item.delivery_address }}</td>
            <td>
              <v-btn color="primary" icon :to="`/orders/${item.id}`" class="mr-2">
                <v-icon>mdi-eye</v-icon>
              </v-btn>

              <v-btn v-if="isModerator" color="primary" icon @click="copy(item)" class="mr-2">
                <v-icon>mdi-content-copy</v-icon>
              </v-btn>

              <v-btn v-if="isModerator" color="warning" icon @click="edit(item)" class="mr-2">
                <v-icon>mdi-pencil</v-icon>
              </v-btn>

              <v-btn v-if="isModerator" color="error" icon @click="destroy(item)">
                <v-icon>mdi-delete</v-icon>
              </v-btn>
            </td>
          </tr>
          </tbody>
        </template>
      </v-simple-table>
      <div class="d-flex mt-4">
        <v-pagination v-model="options.page" :length="pagesCount" class="ml-auto mr-2"/>
        <v-select
            :items="[10,15,30,50, 100,]"
            v-model="options.itemsPerPage"
            outlined
            dense
            hide-details
            label="Показывать на странице"
            style="max-width: 200px;"
        />
      </div>
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
import axios from "@/plugins/axios";
import {mapGetters} from "vuex";
import {orderStatuses, orderStatusLabel, paymentStatuses, paymentStatusLabel} from "@/mixins/StatusHelper";
import BranchPicker from "@/components/Forms/BranchPicker";

export default {
  name: "OrdersView",
  components: {BranchPicker, CustomerPicker, OrderEditor},
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
      statuses: orderStatuses,
      orderStatusLabel: orderStatusLabel,
      paymentStatuses: paymentStatuses,
      paymentStatusLabel: paymentStatusLabel,
    }
  },
  computed: {
    ...mapGetters(['isModerator']),
  },
  methods: {
    copy(item) {
      axios.post(`orders/${item.id}/copy`).then(({order}) => {
        this.onCreated(order);
      })
    },
  }
}
</script>

<style scoped lang="scss">
tr {
  &.info {
    background-color: #99d2ff !important;
    border-color: #99d2ff !important;
  }

  &.success {
    background-color: #c3ffc5 !important;
    border-color: #c3ffc5 !important;
  }

  &.warning {
    background-color: #ffe5c4 !important;
    border-color: #ffe5c4 !important;
  }

  &.moving {
    background-color: #fcd6ff !important;
    border-color: #fcd6ff !important;
  }

  td.error {
    background-color: #ffb2b2 !important;
    border-color: #f79797 !important;
  }

}
</style>