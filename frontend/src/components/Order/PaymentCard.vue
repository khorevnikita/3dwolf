<template>
  <v-card>
    <v-card-title>Платежи</v-card-title>
    <v-card-subtitle>
      Итого: {{ formatPrice(totalSum) }}<br/>
      Осталось: {{ formatPrice(getRemainAmount) }}
    </v-card-subtitle>
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
            <v-list-item-title>{{ formatPrice(payment.amount) }}</v-list-item-title>
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
      <v-pagination
          v-model="page"
          :length="pagesCount"
      />
      <v-spacer/>
      <v-btn text @click="create()">Добавить платёж</v-btn>
    </v-card-actions>

    <v-dialog v-model="editDialog" max-width="500">
      <PaymentEditor
          v-if="editDialog"
          :order_id="order.id"
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
import axios from "@/plugins/axios";
import Swal from "sweetalert2-khonik";

export default {
  name: "PaymentCard",
  components: {PaymentEditor},
  mixins: [ResourceComponentHelper],
  props: ['order', 'totalAmount'],
  data() {
    return {
      resourceKey: "payments",
      resourceApiRoute: `payments`,
      resourceApiParams: `order_id=${this.order.id}`,
      deleteSwalTitle: `Безвозвратно удалить платёж?`,
      page: 1,
      totalSum: 0,
    }
  },
  watch: {
    page() {
      this.query.page = this.page;
      this.getItems();
    }
  },
  computed: {
    getRemainAmount() {
      const remain = this.totalAmount - this.totalSum;
      if (remain <= 0) {
        return 0;
      }
      return remain;
    }
  },
  methods: {
    onCreated(payment) {

      if (this.getRemainAmount > 0 && this.getRemainAmount > payment.amount) {
        if (this.order.payment_status === "not_paid") {
          this.changePaymentStatus("Поменять статус оплаты на \"Частично оплачено\"?", "part_paid")
        }
      } else if (this.getRemainAmount === 0 || this.getRemainAmount <= payment.amount) {
        if (this.order.payment_status !== "full_paid") {
          this.changePaymentStatus("Поменять статус оплаты на \"Полностью оплачено\"?", "full_paid")
        }
      }

      this.getItems();
    },
    changePaymentStatus(title, status) {
      Swal.fire({
        title: title,
        showDenyButton: false,
        showCancelButton: true,
        cancelButtonText: 'Отменить',
        showCloseButton: false,
        showConfirmButton: true,
        confirmButtonText: 'Подтвердить'
      }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
          axios.post(`orders/${this.order.id}/payment-status`, {
            payment_status: status
          }).then(() => {
            this.$emit("statusChanged", status)
          })
        }
      });
    },
    getItems() {
      axios.get(`${this.resourceApiRoute}?${this.resourceApiParams}&${this.setQueryString(this.query)}`).then(body => {
        this.items = body[this.resourceKey];
        this.totalItems = body.totalCount;
        this.pagesCount = body.pagesCount;
        this.totalSum = body.totalSum;
        this.loading = false;
      })
    },
  }
}
</script>

<style scoped>

</style>