<template>
  <v-card>
    <v-card-title v-if="modal">Редактирование наряд-заказа</v-card-title>
    <v-card-text>
      <v-menu
          ref="menu"
          v-model="menu"
          :close-on-content-click="false"
          :return-value.sync="model.date"
          transition="scale-transition"
          offset-y
          min-width="auto"
      >
        <template v-slot:activator="{ on, attrs }">
          <v-text-field
              v-model="model.date"
              label="Дата"
              readonly
              v-bind="attrs"
              v-on="on"
              :error-messages="errors.date"
              :error-count="1"
              :error="!!errors.date"
          />
        </template>
        <v-date-picker
            v-model="model.date"
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
              @click="$refs.menu.save(model.date)"
          >
            OK
          </v-btn>
        </v-date-picker>
      </v-menu>
      <v-autocomplete
          label="Клиент"
          v-model="model.customer_id"
          :items="customers"
          :loading="isLoadingCustomers"
          :search-input.sync="searchCustomer"
          item-value="id"
          item-text="title"
          :error-messages="errors.customer_id"
          :error-count="1"
          :error="!!errors.customer_id"
      />
      <v-text-field
          label="Телефон"
          v-model="model.phone"
          :error-messages="errors.phone"
          :error-count="1"
          :error="!!errors.phone"
      />
      <v-text-field
          label="Сумма"
          v-model="model.amount"
          :error-messages="errors.amount"
          :error-count="1"
          :error="!!errors.amount"
      />
      <v-menu
          ref="menu2"
          v-model="menu2"
          :close-on-content-click="false"
          :return-value.sync="model.deadline"
          transition="scale-transition"
          offset-y
          min-width="auto"
      >
        <template v-slot:activator="{ on, attrs }">
          <v-text-field
              v-model="model.deadline"
              label="Дедлайн"
              readonly
              v-bind="attrs"
              v-on="on"
              :error-messages="errors.deadline"
              :error-count="1"
              :error="!!errors.deadline"
          />
        </template>
        <v-date-picker
            v-model="model.deadline"
            no-title
            scrollable
        >
          <v-spacer></v-spacer>
          <v-btn
              text
              color="primary"
              @click="menu2 = false"
          >
            Отменить
          </v-btn>
          <v-btn
              text
              color="primary"
              @click="$refs.menu2.save(model.deadline)"
          >
            OK
          </v-btn>
        </v-date-picker>
      </v-menu>
      <v-select
          label="Статус"
          v-model="model.status"
          :error-messages="errors.status"
          :error-count="1"
          :error="!!errors.status"
          :items="[
              {value:'new',text:'Новый'},
              {value:'printing',text:'В печати'},
              {value:'shipping',text:'К отгрузке'},
              {value:'complete',text:'Отгружен'},
          ]"
          item-value="value"
          item-text="text"
      />
      <v-select
          label="Статус оплаты"
          v-model="model.payment_status"
          :error-messages="errors.payment_status"
          :error-count="1"
          :error="!!errors.payment_status"
          :items="[
              {value:'not_paid',text:'Не оплачен'},
              {value:'part_paid',text:'Частично оплачен'},
              {value:'full_paid',text:'Полностью оплачен'},
          ]"
          item-value="value"
          item-text="text"
      />
      <v-textarea
          label="Адрес доставки"
          v-model="model.delivery_address"
          :error-messages="errors.delivery_address"
          :error-count="1"
          :error="!!errors.delivery_address"
      />
    </v-card-text>
    <v-card-actions>
      <v-btn v-if="modal" text @click="$emit('close')">Закрыть</v-btn>
      <v-spacer/>
      <v-btn color="primary" @click="save()">Сохранить</v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
import axios from "@/plugins/axios";

export default {
  name: "OrderEditor",
  props: ['value', 'modal'],
  data() {
    return {
      model: this.value,
      modelName: 'order',
      errors: {},
      menu: false,
      menu2: false,
      customers: [],
      isLoadingCustomers: false,
      searchCustomer: ''
    }
  },
  watch: {
    searchCustomer(oldV, newV) {
      if (!newV) return;
      this.getCustomers()
    }
  },
  created() {
    this.getCustomers();
  },
  methods: {
    getCustomers() {
      if (this.isLoadingCustomers) return;
      this.isLoadingCustomers = true;
      axios.get(`customers?search=${this.searchCustomer ? this.searchCustomer : ''}&field=${this.model.customer_id ? this.model.customer_id : ''}`).then(body => {
        this.customers = body.customers;
        this.isLoadingCustomers = false;
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