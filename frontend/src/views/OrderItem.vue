<template>
  <div>
    <v-progress-circular indeterminate v-if="!order"/>
    <div v-else>
      <div class="text-h6 mb-4">Заказ-наряд №{{ order.id }} от {{ moment(order.date).format("DD.MM.YYYY") }}</div>
      <v-row>
        <v-col cols="12" md="6" order-md="2">
          <CustomerCard :customer="order.customer" class="mb-4"/>
          <PaymentCard :payments="order.payments"/>
        </v-col>
        <v-col cols="12" md="6" order-md="1">
          <OrderEditor v-model="order"/>
        </v-col>
        <v-col cols="12" order="3">
          <div class="d-flex align-items-center">
            <div class="text-h6">Позиции заказов</div>
            <v-spacer/>
            <v-btn small @click="create()" color="primary">Добавить</v-btn>
          </div>
          <v-data-table
              :headers="headers"
              :items="items"
              :options.sync="options"
              :server-items-length="totalItems"
              :loading="loading"
              class="elevation-1 mt-3"
          >
            <template v-slot:[`item.part_id`]="{item}">
              {{ item.part?`${item.part.material.name} (${item.part.manufacturer.name} ${item.part.prod_number})`:'-' }}
            </template>

            <template v-slot:[`item.actions`]="{item}">
              <v-btn color="warning" icon @click="edit(item)">
                <v-icon>mdi-pencil</v-icon>
              </v-btn>

              <v-btn color="error" icon @click="destroy(item)">
                <v-icon>mdi-delete</v-icon>
              </v-btn>
            </template>
          </v-data-table>
          <div class="text-h6 mt-4 mb-5">
            Итого: {{totalAmount}} руб.
            <br/>
            Вес: {{totalWeight}}
          </div>
        </v-col>
      </v-row>
    </div>
    <v-dialog v-model="editDialog" max-width="700">
      <OrderLineEditor
          v-if="editDialog"
          @close="editDialog=false"
          v-model="editItem"
          @created="onCreated"
          @updated="onUpdated"
          :modal="true"
      />
    </v-dialog>
  </div>
</template>

<script>
import axios from "@/plugins/axios";
import moment from "moment";
import OrderEditor from "@/components/Order/OrderEditor";
import CustomerCard from "@/components/Order/CustomerCard";
import PaymentCard from "@/components/Order/PaymentCard";
import ResourceComponentHelper from "@/mixins/ResourceComponentHelper";
import OrderLineEditor from "@/components/Order/OrderLineEditor";

export default {
  name: "OrderItem",
  components: {OrderLineEditor, PaymentCard, CustomerCard, OrderEditor},
  mixins: [ResourceComponentHelper],
  data() {
    return {
      order_id: Number(this.$route.params.id),
      order: undefined,
      moment: moment,
      errors: {},
      headers: [
        {text: "№", value: "number", sortable: true},
        {text: "Название", value: "name", sortable: true},
        {text: "Деталь", value: "part_id", sortable: true},
        {text: "Кол-во", value: "count", sortable: true},
        {text: "Вес", value: "weight", sortable: true},
        {text: "Общ. вес", value: "total_weight", sortable: true},
        {text: "Цена", value: "price", sortable: true},
        {text: "Стоимость", value: "total_amount", sortable: true},
        {text: "Прод. печати", value: "print_duration", sortable: true},
        {text: "", value: "actions", sortable: false},
      ],
      resourceKey: "orderLines",
      resourceApiRoute: `orders/${this.$route.params.id}/order-lines`,
      deleteSwalTitle: `Безвозвратно удалить позицию?`,
    }
  },
  created() {
    this.getOrder();
  },
  computed: {
    customer() {
      return this.order?.customer;
    },
    totalAmount(){
      return this.items.reduce((acc,item)=>acc+=item.total_amount,0)
    },
    totalWeight() {
      return this.items.reduce((acc,item)=>acc+=item.total_weight,0);
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