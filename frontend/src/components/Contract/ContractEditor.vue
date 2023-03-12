<template>
  <v-card>
    <v-card-title>Редактирование договора</v-card-title>
    <v-card-text>
      <v-text-field
          label="Номер"
          v-model="model.number"
          :error-messages="errors.number"
          :error-count="1"
          :error="!!errors.number"
      />
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
          ></v-text-field>
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
      <v-select
          label="Статус"
          v-model="model.status"
          :error-messages="errors.status"
          :error-count="1"
          :error="!!errors.status"
          :items="[
              {value:'new',text:'Новый'},
              {value:'process',text:'В работе'},
              {value:'complete',text:'Завершен'},
          ]"
          item-value="value"
          item-text="text"
      />
      <v-text-field
          label="Сумма"
          v-model="model.amount"
          :error-messages="errors.amount"
          :error-count="1"
          :error="!!errors.amount"
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
  name: "ContractEditor",
  props: ['value', 'modal'],
  data() {
    return {
      model: this.value,
      modelName: 'contract',
      errors: {},
      menu: false,
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