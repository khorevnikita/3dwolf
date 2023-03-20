<template>
  <div>
    <v-row class="mt-3">
      <v-col cols="12" md="6">
        <v-select
            label="Год"
            :items="[2023]"
            v-model="year"
            dense
            hide-details
        />
      </v-col>
      <v-col cols="12" md="6" class="text-right">
        <v-btn color="primary" @click="create()">Добавить платёж</v-btn>
      </v-col>
      <v-col cols="12" md="6">
        <v-card>
          <v-card-subtitle>
            Оборотка
          </v-card-subtitle>
          <v-card-title>
            {{ formatPrice(data.income) }}
          </v-card-title>
        </v-card>
      </v-col>
      <v-col cols="12" md="6">
        <v-card>
          <v-card-subtitle>
            Остаток
          </v-card-subtitle>
          <v-card-title>
            {{ formatPrice(data.balance) }}
          </v-card-title>
        </v-card>
      </v-col>

      <v-col cols="12" md="6">
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
                <tr v-for="(monthData,index) in data.months" :key="index">
                  <td>{{ months[monthData.month - 1] }}</td>
                  <td class="income">{{ formatPrice(monthData.income) }}</td>
                  <td class="expense">{{ formatPrice(monthData.expense) }}</td>
                </tr>
                </tbody>
              </template>
            </v-simple-table>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="12" md="6">
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
                <tr v-for="account in data.accounts" :key="account.id">
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

        <v-card class="mt-4">
          <v-card-text>
            <v-simple-table>
              <template v-slot:default>
                <thead>
                <tr>
                  <th class="text-left">
                    Сотрудник
                  </th>
                  <th class="text-left">
                    Баланс
                  </th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="user in data.users" :key="user.id">
                  <td>{{ user.name }} {{ user.surname }}</td>
                  <td v-bind:class="{'income':user.balance>0,'expense':user.balance<0}">
                    {{ formatPrice(user.balance) }}
                  </td>
                </tr>
                </tbody>
              </template>
            </v-simple-table>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
    <h5 class="text-h5 mt-4 mb-3">Все платежи</h5>
    <v-data-table
        :headers="headers"
        :items="items"
        :options.sync="options"
        :server-items-length="totalItems"
        :loading="loading"
        class="elevation-1 mt-3"
    >
      <template v-slot:[`item.type`]="{item}">
        <v-icon :color="item.type==='income'?'success':'error'">
          {{ item.type === 'income' ? 'mdi-chevron-up' : 'mdi-chevron-down' }}
        </v-icon>
      </template>
      <template v-slot:[`item.amount`]="{item}">
        {{ formatPrice(item.amount) }}
      </template>
      <template v-slot:[`item.user_id`]="{item}">
        {{ item.user ? `${item.user.name} ${item.user.surname}` : '-' }}
      </template>

      <template v-slot:[`item.order_id`]="{item}">
        {{ item.order_id ? `Заказ №${item.order_id}` : '-' }}
      </template>
      <template v-slot:[`item.account_id`]="{item}">
        {{ item.account ? item.account.name : '-' }}
      </template>
      <template v-slot:[`item.paid_at`]="{item}">
        {{ item.paid_at ? moment(item.paid_at).format("DD.MM.YYYY") : '-' }}
      </template>
      <template v-slot:[`item.actions`]="{item}">
        <v-btn color="warning" icon @click="edit(item);">
          <v-icon>mdi-pencil</v-icon>
        </v-btn>

        <v-btn color="error" icon @click="destroy(item,()=>{
          getData();
          getItems();
        })">
          <v-icon>mdi-delete</v-icon>
        </v-btn>
      </template>
    </v-data-table>

    <v-dialog v-model="editDialog" max-width="500">
      <PaymentEditor
          v-if="editDialog"
          @close="editDialog=false"
          @created="i=>{
            onCreated(i);
            getData();
            getItems();
          }"
          :show-user="true"
          v-model="editItem"
      />
    </v-dialog>
  </div>
</template>

<script>
import axios from "@/plugins/axios";
import PaymentEditor from "@/components/Payment/PaymentEditor";
import {formatPrice} from "@/plugins/formats";
import ResourceComponentHelper from "@/mixins/ResourceComponentHelper";

export default {
  name: "MoneyView",
  components: {PaymentEditor},
  mixins: [ResourceComponentHelper],
  data() {
    return {
      year: 2023,
      months: [
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
      data: {},
      addPaymentDialog: false,
      formatPrice: formatPrice,
      resourceKey: "payments",
      resourceApiRoute: `payments`,
      deleteSwalTitle: `Безвозвратно удалить платёж`,
      headers: [
        {text: "ID", value: "id", sortable: false},
        {text: "Тип", value: "type", sortable: false},
        {text: "Дата", value: "paid_at", sortable: false},
        {text: "Пользователь", value: "user_id", sortable: false},
        {text: "Заказ", value: "order_id", sortable: false},
        {text: "Аккаунт", value: "account_id", sortable: false},
        {text: "Сумма", value: "amount", sortable: false},
        {text: "Описание", value: "description", sortable: false},
        {text: "", value: "actions", sortable: false},
      ],
    }
  },
  created() {
    this.getData();
  },
  watch: {
    year() {
      this.getData();
    }
  },
  methods: {
    getData() {
      axios.get(`money/statistics?year=${this.year}`).then(body => {
        this.data = body.data;
      })
    }
  }
}
</script>

<style scoped>
.income {
  background: #dfffae;
}

.expense {
  background: #ffaed0;
}

.expecting {
  background: #ebebeb;
}
</style>