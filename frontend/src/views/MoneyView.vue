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
        <v-btn color="primary" @click="addPaymentDialog=true">Добавить платёж</v-btn>
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
                  <td>{{ formatPrice(monthData.income) }}</td>
                  <td>{{ formatPrice(monthData.expense) }}</td>
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
                  <td>{{ formatPrice(account.balance) }}</td>
                  <td>{{ formatPrice(account.expected_income) }}</td>
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
                  <td>{{ formatPrice(user.balance) }}</td>
                </tr>
                </tbody>
              </template>
            </v-simple-table>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <v-dialog v-model="addPaymentDialog" max-width="500">
      <PaymentEditor
          @close="addPaymentDialog=false"
          @created="getData()"
          :show-user="true"
      />
    </v-dialog>
  </div>
</template>

<script>
import axios from "@/plugins/axios";
import PaymentEditor from "@/components/Payment/PaymentEditor";
import {formatPrice} from "@/plugins/formats";

export default {
  name: "MoneyView",
  components: {PaymentEditor},
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
      addPaymentDialog:false,
      formatPrice:formatPrice,
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

</style>