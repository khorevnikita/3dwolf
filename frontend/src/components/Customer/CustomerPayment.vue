<template>
  <v-card>
    <v-card-subtitle>Баланс</v-card-subtitle>
    <v-card-title>{{ formatPrice(customer.balance) }}</v-card-title>
    <v-card-text>
      <v-alert type="info" v-if="items.length===0">Платежей не найдено</v-alert>
      <v-list v-else>
        <v-list-item v-for="item in items" :key="item.id">
          <v-list-item-content>
            <v-list-item-title> Платёж №{{ item.id }}: {{ formatPrice(item.amount) }}</v-list-item-title>
            <v-list-item-subtitle>от {{ item.paid_at ? moment(item.paid_at).format("DD.MM.YYYY") : '-' }}
            </v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-card-text>
    <v-card-actions>
      <v-pagination
          v-model="page"
          :length="pagesCount"
      />
    </v-card-actions>
  </v-card>
</template>

<script>

import ResourceComponentHelper from "@/mixins/ResourceComponentHelper";

export default {
  name: "CustomerPayment",
  props: ['customer'],
  mixins: [ResourceComponentHelper],
  data() {
    return {
      resourceKey: "payments",
      resourceApiRoute: `payments`,
      resourceApiParams: `customer_id=${this.customer.id}`,
      page: 1,
    }
  },
  watch: {
    page() {
      this.query.page=this.page;
      this.getItems();
    }
  }
}
</script>

<style scoped>

</style>