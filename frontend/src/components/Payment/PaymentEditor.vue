<template>
  <v-card>
    <v-card-title>Редактирование платежа</v-card-title>
    <v-card-text>
      <v-select
          v-if="!incomeOnly"
          label="Тип"
          v-model="model.type"
          :items="[
              {value:'expense',text:'Расход'},
              {value:'income',text:'Приход'},
              {value:'exchange',text:'Перевод'},
          ]"
          item-value="value"
          item-text="text"
          :error-messages="errors.type"
          :error-count="1"
          :error="!!errors.type"

      />
      <v-select
          v-if="showUser && model.type !== 'exchange'"
          label="Пользователь"
          v-model="model.user_id"
          :items="users"
          item-value="id"
          item-text="name"
      >
        <template v-slot:selection="{ item, index }">
          {{ item.name }} {{ item.surname }}
        </template>
        <template v-slot:item="{ item, index }">
          {{ item.name }} {{ item.surname }}
        </template>

      </v-select>
      <v-select
          v-if="model.type === 'exchange'"
          label="Старый счет"
          v-model="model.source_account_id"
          :items="accounts"
          item-value="id"
          item-text="name"
          :error-messages="errors.source_account_id"
          :error-count="1"
          :error="!!errors.source_account_id"
      />

      <v-select
          label="Счёт"
          v-model="model.account_id"
          :items="accounts"
          item-value="id"
          item-text="name"
          :error-messages="errors.account_id"
          :error-count="1"
          :error="!!errors.account_id"
      />

      <v-switch
          v-if="!order_id && model.type !== 'exchange'"
          label="По наряд-заказу"
          v-model="showOrder"
          dense
      />
      <v-autocomplete
          v-if="showOrder && model.type !== 'exchange' "
          label="Наряд-заказ"
          v-model="model.order_id"
          :items="orders"
          :loading="isLoadingOrders"
          :search-input.sync="searchOrder"
          item-value="id"
          :error-messages="errors.order_id"
          :error-count="1"
          :error="!!errors.order_id"
      >
        <template #item="{item}">Заказ №{{ item.id }}</template>
        <template #selection="{item}">Заказ №{{ item.id }}</template>
      </v-autocomplete>

      <v-text-field
          label="Сумма"
          v-model="model.amount"
          :error-messages="errors.amount"
          :error-count="1"
          :error="!!errors.amount"
      />

      <v-menu
          ref="menu"
          v-model="menu"
          :close-on-content-click="false"
          :return-value.sync="model.paid_at"
          transition="scale-transition"
          offset-y
          min-width="auto"
      >
        <template v-slot:activator="{ on, attrs }">
          <v-text-field
              v-model="model.paid_at"
              label="Дата платежа"
              readonly
              v-bind="attrs"
              v-on="on"
              :error-messages="errors.paid_at"
              :error-count="1"
              :error="!!errors.paid_at"
          ></v-text-field>
        </template>
        <v-date-picker
            v-model="model.paid_at"
            no-title
            scrollable
        >
          <v-spacer></v-spacer>
          <v-btn
              text
              color="primary"
              @click="menu = false"
          >
            Отменить
          </v-btn>
          <v-btn
              text
              color="primary"
              @click="$refs.menu.save(model.paid_at)"
          >
            OK
          </v-btn>
        </v-date-picker>
      </v-menu>

      <v-textarea
          v-if="!autoDescription && model.type !== 'exchange'"
          label="Комментарий к платежу"
          v-model="model.description"
          :error-messages="errors.description"
          :error-count="1"
          :error="!!errors.description"
      />
    </v-card-text>
    <v-card-actions>
      <v-btn text @click="$emit('close')">Закрыть</v-btn>
      <v-spacer/>
      <v-btn color="primary" @click="save()">Сохранить</v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
import axios from "@/plugins/axios";

export default {
  name: "PaymentEditor",
  props: ['value', 'order_id', 'showUser', 'incomeOnly', 'autoDescription'],
  data() {
    return {
      model: this.value ? this.value : {
        order_id: this.order_id,
        type: 'income'
      },
      modelName: 'payment',
      errors: {},
      users: [],
      accounts: [],
      menu: false,
      showOrder: !this.order_id,
      searchOrder: "",
      isLoadingOrders: false,
      orders: []
    }
  },
  created() {
    this.model.order_id = this.model.order_id ? this.model.order_id : this.order_id;
    if (this.autoDescription) this.model.description = `Оплата по наряд-заказу №${this.model.order_id}`;
    if (this.incomeOnly) this.model.type = 'income';

    this.getUsers();
    this.getAccounts();
  },
  watch: {
    searchOrder() {
      this.getOrders();
    }
  },
  methods: {
    getUsers() {
      axios.get(`users`).then(body => this.users = body.users);
    },
    getAccounts() {
      axios.get(`accounts`).then(body => this.accounts = body.accounts)
    },
    getOrders() {
      if (this.isLoadingOrders) return;
      this.isLoadingOrders = true;
      axios.get(`orders?search=${this.searchOrder ? this.searchOrder : ''}&sort_order_id=${this.model.order_id ? this.model.order_id : ''}`).then(body => {
        this.orders = body.orders;
        this.isLoadingOrders = false;
      })
    },
    save() {
      this.errors = {};
      if (this.model.id) {
        this.update();
      } else {
        this.store();
      }
    },
    store() {
      if (this.model.type === 'exchange') {
        this.model.description = "Перевод ДС";
      }
      axios.post(`${this.modelName}s`, this.model).then(body => {
        this.$emit("created", body[this.modelName]);
        this.$emit("close");
      }).catch(err => {
        this.errors = err.body.errors;
      })
    },
    update() {
      axios.put(`${this.modelName}s/${this.model.id}`, this.model).then(body => {
        this.$emit("updated", body[this.modelName]);
        this.$emit("close");
      }).catch(err => {
        this.errors = err.body.errors;
      })
    }
  }
}
</script>

<style scoped>

</style>