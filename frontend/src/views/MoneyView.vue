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
        <MonthMoneyTable :months="data.months"/>
      </v-col>
      <v-col cols="12" md="6">
        <MoneyAccountTable :accounts="data.accounts"/>

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
            <v-select
                label="Тип"
                v-model="query.type"
                dense
                hide-details
                :items="[
                      {value:'',text:'Все'},
                      {value:'income',text:'Приход'},
                      {value:'expense',text:'Расход'},

                      ]"
                item-value="value"
                item-text="text"
            />
          </v-col>
          <v-col cols="12" md="3">
            <UserPicker
                :dense="true"
                v-model="query.user_id"
            />
          </v-col>
          <v-col cols="12" md="3">
            <v-select
                label="Счёт"
                v-model="query.account_id"
                :items="accountsFilterList"
                item-value="id"
                item-text="name"
                dense
                hide-details
            />
          </v-col>
          <v-col cols="12" md="3">
            <v-menu
                ref="menu"
                v-model="menu"
                :close-on-content-click="false"
                :return-value.sync="query.date"
                transition="scale-transition"
                offset-y
                min-width="auto"
            >
              <template v-slot:activator="{ on, attrs }">
                <v-text-field
                    v-model="query.date"
                    label="Дата платежа"
                    prepend-icon="mdi-calendar"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                    clearable
                ></v-text-field>
              </template>
              <v-date-picker
                  v-model="query.date"
                  no-title
                  scrollable
                  range
              >
                <v-spacer></v-spacer>
                <v-btn
                    text
                    color="primary"
                    @click="menu = false"
                >
                  Cancel
                </v-btn>
                <v-btn
                    text
                    color="primary"
                    @click="$refs.menu.save(query.date)"
                >
                  OK
                </v-btn>
              </v-date-picker>
            </v-menu>
          </v-col>
          <v-col cols="12" md="3">
            <PaymentPurposePicker v-model="query.payment_purpose_id"/>
          </v-col>
        </v-row>
      </v-card-text>
      <v-card-actions>
        <v-btn color="primary" @click="replaceRoute">Найти</v-btn>
      </v-card-actions>
    </v-card>
    <v-data-table
        :headers="headers"
        :items="items"
        :options.sync="options"
        :server-items-length="totalItems"
        :loading="loading"
        class="elevation-1 mt-3"
    >
      <template v-slot:[`item.description`]="{item}">
        <v-chip
            v-if="item.purpose"
            :color="item.purpose.color"
            outlined
            x-small
            class="mb-1"
        >
          {{ item.purpose.name }}
        </v-chip>
        <div>{{ item.description }}</div>
      </template>
      <template v-slot:[`item.type`]="{item}">
        <v-icon color="success" v-if="item.type === 'income'">mdi-chevron-up</v-icon>
        <v-icon color="error" v-if="item.type === 'expense'">mdi-chevron-down</v-icon>
        <v-icon color="warning" v-if="item.type==='exchange'">
          mdi-unfold-more-horizontal
        </v-icon>
      </template>
      <template v-slot:[`item.amount`]="{item}">
        {{ formatPrice(item.amount) }}
      </template>
      <template v-slot:[`item.user_id`]="{item}">
        {{ item.user ? `${item.user.name} ${item.user.surname}` : '-' }}
      </template>

      <template v-slot:[`item.order_id`]="{item}">
        <router-link v-if="item.order_id" :to="`/orders/${item.order_id}`">Заказ №{{ item.order_id }}</router-link>
      </template>
      <template v-slot:[`item.account_id`]="{item}">
        {{ item.source_account ? `${item.source_account.name} ->` : '' }}
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
    <div class="text-subtitle mt-2">Итого: <b>{{ formatPrice(totalSum) }}</b></div>

    <v-dialog v-model="editDialog" max-width="500">
      <PaymentEditor
          v-if="editDialog"
          @close="editDialog=false"
          @created="i=>{
            onCreated(i);
            getData();
            getItems();
          }"
          @updated="i=>{
            onUpdated(i);
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
import MonthMoneyTable from "@/components/Dashboard/MonthMoneyTable";
import MoneyAccountTable from "@/components/Dashboard/MoneyAccountTable";
import UserPicker from "@/components/Forms/UserPicker";
import PaymentPurposePicker from "@/components/Forms/PaymentPurposePicker";

export default {
  name: "MoneyView",
  components: {PaymentPurposePicker, UserPicker, MoneyAccountTable, MonthMoneyTable, PaymentEditor},
  mixins: [ResourceComponentHelper],
  data() {
    return {
      year: 2023,
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
      accounts: [],
      menu: false,
      totalSum: 0,
    }
  },
  created() {
    this.getData();
    this.getAccounts();
  },
  watch: {
    year() {
      this.getData();
    },
    $route: {
      handler() {
        this.readRoute();
      }, deep: true
    }
  },
  computed: {
    accountsFilterList() {
      return [
        {id: '', name: 'Все'},
        ...this.accounts.map(a => {
          return {
            id: String(a.id),
            name: a.name,
          }
        })
      ]
    },
  },
  methods: {
    getItems() {
      axios.get(`${this.resourceApiRoute}?${this.resourceApiParams}&${this.setQueryString(this.query)}`).then(body => {
        this.items = body[this.resourceKey];
        this.totalItems = body.totalCount;
        this.pagesCount = body.pagesCount;
        this.totalSum = body.totalSum;
        this.loading = false;
      })
    },
    getAccounts() {
      axios.get(`accounts`).then(body => this.accounts = body.accounts)
    },
    getData() {
      axios.get(`money/statistics?year=${this.year}`).then(body => {
        this.data = body.data;
      })
    }
  }
}
</script>

<style scoped>

</style>