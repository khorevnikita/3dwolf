<template>
  <div>
    <div class="text-h6 mb-4">Заказ-наряд №{{ order.id }} от {{ moment(order.date).format("DD.MM.YYYY") }}</div>
    <v-row>
      <v-col cols="12" md="4" order-md="2">
        <CustomerCard
            v-if="order.customer"
            :customer="order.customer"
            class="mb-4"
        />
        <PaymentCard
            :payments="order.payments"
        />
      </v-col>
      <v-col cols="12" md="8" order-md="1">
        <OrderEditor v-model="order"/>
      </v-col>
    </v-row>
  </div>
</template>

<script>
import axios from "@/plugins/axios";
import moment from "moment";
import OrderEditor from "@/components/Order/OrderEditor";
import CustomerCard from "@/components/Order/CustomerCard";
import PaymentCard from "@/components/Order/PaymentCard";

export default {
  name: "OrderItem",
  components: {PaymentCard, CustomerCard, OrderEditor},
  data() {
    return {
      order_id: Number(this.$route.params.id),
      order: undefined,
      moment: moment,
      errors: {}
    }
  },
  created() {
    this.getOrder();
  },
  computed: {
    customer() {
      return this.order?.customer;
    }
  },
  methods: {
    getOrder() {
      axios.get(`orders/${this.order_id}`).then(body => {
        this.order = body.order;
      })
    }
  }
}
</script>

<style scoped>

</style>