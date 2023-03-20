<template>
  <v-card>
    <v-card-title>Наряд-заказы</v-card-title>
    <v-card-text>
      <v-alert type="info" v-if="items.length===0">Заказов не найдено</v-alert>
      <v-list v-else>
        <v-list-item v-for="item in items" :key="item.id">
          <v-list-item-content>
            <v-list-item-title> Заказ №{{ item.id }}</v-list-item-title>
            <v-list-item-subtitle>от {{ moment(item.date).format("DD.MM.YYYY") }}</v-list-item-subtitle>
          </v-list-item-content>
          <v-list-item-action>
            <v-btn link :to="`/orders/${item.id}`" icon>
              <v-icon>mdi-eye</v-icon>
            </v-btn>
          </v-list-item-action>
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
  name: "CustomerOrder",
  props: ['customerId'],
  mixins: [ResourceComponentHelper],
  data() {
    return {
      resourceKey: "orders",
      resourceApiRoute: `orders`,
      resourceApiParams: `customer_id=${this.customerId}`,
      page: 1,
    }
  }, watch: {
    page() {
      this.query.page = this.page;
      this.getItems();
    }
  }
}
</script>

<style scoped>

</style>