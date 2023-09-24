<template>
  <v-autocomplete
      label="Клиент"
      v-model="customer_id"
      :items="customers"
      :loading="isLoadingCustomers"
      :search-input.sync="searchCustomer"
      item-value="id"
      item-text="title"
      :error-messages="error"
      :error-count="1"
      :error="!!error"
      :clearable="!readonly"
      :filter="()=>true"
      :dense="dense"
      :hide-details="dense"
      :readonly="readonly"
  >
    <template #item="{item}">
      {{ item.title }}, {{ item.phone }}, {{ item.email }}
    </template>
  </v-autocomplete>
</template>

<script>
import axios from "@/plugins/axios";

export default {
  name: "CustomerPicker",
  props: ['value', 'error', 'dense','readonly'],
  data() {
    return {
      customer_id: this.value,
      customers: [],
      isLoadingCustomers: false,
      searchCustomer: ''
    }
  },
  watch: {
    searchCustomer(oldV, newV) {
      if (!newV) return;
      this.getCustomers()
    },
    customer_id: function (customerId) {
      this.$emit('input', customerId);
      const customer = this.customers.find(c => c.id === customerId);
      if (customer) {
        this.$emit('selected', customer);
      }
    }
  },
  created() {
    this.getCustomers();
  },
  methods: {
    getCustomers() {
      if (this.isLoadingCustomers) return;
      this.isLoadingCustomers = true;
      axios.get(`customers?search=${this.searchCustomer ? this.searchCustomer : ''}&field=${this.customer_id ? this.customer_id : ''}`).then(body => {
        this.customers = body.customers;
        this.isLoadingCustomers = false;
      })
    },
  }
}
</script>

<style scoped>

</style>