<template>
  <v-card>
    <v-card-title>Платежи</v-card-title>
    <v-card-text>
      <v-alert type="info" v-if="items.length===0">Платежей нет</v-alert>
      <v-list v-else>
        <v-list-item v-for="payment in items" :key="payment.id"
                     v-bind:class="{'success--text':payment.type==='income','error--text':payment.type==='expense'}">
          <v-list-item-icon>
            <v-icon color="success" v-if="payment.type==='income'">
              mdi-chevron-up
            </v-icon>
            <v-icon color="error" v-if="payment.type==='expense'">
              mdi-chevron-down
            </v-icon>
          </v-list-item-icon>
          <v-list-item-content>
            <v-list-item-title>{{ payment.amount }}</v-list-item-title>
            <v-list-item-subtitle>
              от {{ payment.paid_at ? moment(payment.paid_at).format("DD.MM.YYYY") : '-' }} на
              {{ payment.account ? payment.account.name : '-' }}
            </v-list-item-subtitle>
            <v-list-item-subtitle>{{ payment.description }}</v-list-item-subtitle>
          </v-list-item-content>
          <v-list-item-action>
            <v-btn icon color="warning" @click="edit(payment)">
              <v-icon>mdi-pencil</v-icon>
            </v-btn>
            <v-btn icon color="error" @click="destroy(payment)">
              <v-icon>mdi-delete</v-icon>
            </v-btn>
          </v-list-item-action>
        </v-list-item>
      </v-list>
    </v-card-text>
    <v-card-actions>
      <v-spacer/>
      <v-btn text @click="create()">Добавить платёж</v-btn>
    </v-card-actions>

    <v-dialog v-model="editDialog" max-width="500">
      <PaymentEditor
          v-if="editDialog"
          :order_id="orderId"
          @close="editDialog=false"
          @created="onCreated"
          v-model="editItem"
          :income-only="true"
          :auto-description="true"
      />
    </v-dialog>
  </v-card>
</template>

<script>
import PaymentEditor from "@/components/Payment/PaymentEditor";
import ResourceComponentHelper from "@/mixins/ResourceComponentHelper";

export default {
  name: "PaymentCard",
  components: {PaymentEditor},
  mixins: [ResourceComponentHelper],
  props: ['orderId'],
  data() {
    return {
      resourceKey: "payments",
      resourceApiRoute: `payments`,
      resourceApiParams: `order_id=${this.orderId}`,
      deleteSwalTitle: `Безвозвратно удалить платёж?`,
    }
  },
  methods: {}
}
</script>

<style scoped>

</style>