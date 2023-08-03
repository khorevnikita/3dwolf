<template>
  <div>
    <v-progress-circular indeterminate v-if="!order"/>
    <div v-else>
      <div class="d-flex align-items-center justify-space-between">
        <div class="d-flex">
          <div class="text-h6 mb-4">
            Заказ-наряд №{{ order.id }} от {{ moment(order.date).format("DD.MM.YYYY") }}
          </div>
          <v-btn color="primary" icon @click="qr()" class="ml-2">
            <v-icon>mdi-qrcode</v-icon>
          </v-btn>
        </div>
        <div>
          <v-btn color="error" class="mr-2" @click="exportFile('pdf')">
            <v-icon>mdi-file-pdf-box</v-icon>&nbsp;
            Скачать
          </v-btn>
          <!--<v-btn color="success" @click="exportFile('xlsx')">
            <v-icon>mdi-microsoft-excel</v-icon>&nbsp;
            Экспорт
          </v-btn>-->
        </div>
      </div>
      <v-row>
        <v-col cols="12" md="6" order-md="2">
          <CustomerCard v-if="isModerator" :customer="order.customer" class="mb-4"/>
          <FilesCard :order-id="order.id" class="mb-4"/>
          <PaymentCard v-if="showMoney" :order="order" :total-amount="totalAmountAfterDiscount"/>
        </v-col>
        <v-col cols="12" md="6" order-md="1">
          <OrderEditor v-if="isModerator" v-model="order"/>
          <OrderViewer v-else :order="order"/>
          <OrderNotification v-if="isModerator" :order="order" class="mt-4"/>
        </v-col>
        <v-col cols="12" order="3">
          <div class="d-flex align-items-center">
            <div class="text-h6">Позиции заказов</div>
            <v-spacer/>
            <v-btn v-if="isModerator" small @click="estimateSelector=true" class="mr-2" color="secondary">Загрузить из
              сметы
            </v-btn>
            <v-btn v-if="isModerator" small @click="create()" color="primary">Добавить</v-btn>
          </div>
          <v-data-table
              :headers="headers"
              :items="items"
              :options.sync="options"
              :server-items-length="totalItems"
              :loading="loading"
              class="elevation-1 mt-3"
          >
            <template v-slot:[`item.index`]="{item}">
              {{ items.indexOf(item) + 1 }}
            </template>
            <template v-slot:[`item.name`]="{item}">
              {{ item.name }} <span v-if="item.filling">({{ item.filling }}%)</span>
            </template>
            <template v-slot:[`item.part_id`]="{item}">
              {{ item.part ? `${item.part.material.name}, ${item.part.color} (${item.part.prod_number})` : '-' }}
              <div style="font-size: 0.8em">{{item.part.prod_number}}</div>
            </template>
            <template v-slot:[`item.price`]="{item}">
              {{ formatPrice(item.price) }}
            </template>
            <template v-slot:[`item.total_amount`]="{item}">
              {{ formatPrice(item.total_amount) }}
            </template>
            <template v-slot:[`item.total_weight`]="{item}">
              {{ Math.round(item.total_weight * 100) / 100 }}
            </template>
            <template v-slot:[`item.weight`]="{item}">
              {{ Math.round(item.weight * 100) / 100 }}
            </template>
            <template v-slot:[`item.print_duration`]="{item}">
              {{ formatDuration(item.print_duration) }}
            </template>

            <template v-slot:[`item.actions`]="{item}">
              <v-btn color="primary" icon @click="copy(item)" class="mr-2" v-if="isModerator">
                <v-icon>mdi-content-copy</v-icon>
              </v-btn>

              <v-btn color="warning" icon @click="edit(item)" class="mr-2" v-if="isModerator">
                <v-icon>mdi-pencil</v-icon>
              </v-btn>

              <v-btn color="error" icon @click="destroy(item)" v-if="isModerator">
                <v-icon>mdi-delete</v-icon>
              </v-btn>
            </template>
          </v-data-table>
          <v-row class="mt-4" v-if="isModerator">
            <v-col cols="12" md="6" lg="4">
              <v-text-field
                  label="Скидка (%)"
                  v-model="order.discount"
                  dense
              >
                <template #append-outer>
                  <v-btn text small @click="setDiscount()">Применить</v-btn>
                </template>
              </v-text-field>
            </v-col>
          </v-row>
          <div class="text-h6 mt-4 mb-5">
            Итого: {{ formatPrice(totalAmount) }}
            <br/>
            Итого с учетом скидки: {{ formatPrice(totalAmountAfterDiscount) }}
            <br/>
            Вес: {{ totalWeight }} <br/>
            Время печати: {{ formatDuration(totalTime) }}
          </div>
        </v-col>
      </v-row>
    </div>
    <v-dialog v-model="editDialog" max-width="700">
      <OrderLineEditor
          v-if="editDialog"
          @close="editDialog=false"
          v-model="editItem"
          @created="getItems"
          @updated="onUpdated"
          :modal="true"
      />
    </v-dialog>
    <v-dialog v-model="estimateSelector" max-width="700">
      <EstimateModal
          v-if="estimateSelector"
          @selected="getItems()"
          @close="estimateSelector=false"
          :order-id="order_id"
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
import FilesCard from "@/components/Order/FIlesCard";
import Swal from "sweetalert2-khonik";
import OrderNotification from "@/components/Order/OrderNotification";
import {mapGetters} from "vuex";
import OrderViewer from "@/components/Order/OrderViewer";
import EstimateModal from "@/components/Order/EstimateModal";

export default {
  name: "OrderItem",
  components: {
    EstimateModal,
    OrderViewer, OrderNotification, FilesCard, OrderLineEditor, PaymentCard, CustomerCard, OrderEditor
  },
  mixins: [ResourceComponentHelper],
  data() {
    return {
      order_id: Number(this.$route.params.id),
      order: undefined,
      moment: moment,
      errors: {},
      headers: [
        {text: "№", value: "index", sortable: false},
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
      endpoint: process.env.VUE_APP_API_ENDPOINT,
      estimateSelector: false,
    }
  },
  created() {
    this.getOrder();
  },
  computed: {
    ...mapGetters(['user', 'isModerator']),
    showMoney() {
      return this.user && this.user.permission?.includes('payments');
    },
    customer() {
      return this.order?.customer;
    },
    totalAmount() {
      const amount = this.items.reduce((acc, item) => acc += item.total_amount, 0);

      return Math.round(amount * 100) / 100;
    },
    totalAmountAfterDiscount() {
      if (!this.order.discount) {
        return this.totalAmount;
      }
      const discount = Number(this.order.discount);
      let amount = this.totalAmount;
      amount = amount * (1 - discount / 100);
      return Math.round(amount * 100) / 100;
    },
    totalWeight() {
      const weight = this.items.reduce((acc, item) => acc += item.total_weight, 0);
      return Math.round(weight * 100) / 100;
    },
    totalTime() {
      const time = this.items.reduce((acc, item) => acc += (item.print_duration * item.count), 0);
      return Math.round(time * 100) / 100;
    },
  },
  methods: {
    copy(item) {
      axios.post(`orders/${this.order_id}/order-lines/${item.id}/copy`).then(({orderLine}) => {
        this.onCreated(orderLine);
      })
    },
    getOrder() {
      axios.get(`orders/${this.order_id}`).then(body => {
        this.order = body.order;
      })
    },
    exportFile(type) {
      axios.get(`orders/${this.order_id}/export-auth?type=${type}`).then(body => {
        window.open(body.download_link, '_blank')
      })
    },
    onUpdated(orderLine) {
      const idx = this.items.findIndex(i => i.id === orderLine.id);
      this.items.splice(idx, 1, orderLine);
    },
    setDiscount() {
      this.errors = {};
      axios.post(`orders/${this.order_id}/set-discount`, {
        discount: this.order.discount
      }).then((body) => {
        this.order = body.order;
        Swal.fire("Скидка применена")
      }).catch(err => {
        this.errors = err.body.errors;
      })
    },
    qr() {
      axios.get(`orders/${this.order_id}/qr`).then(({url}) => {
        window.open(url, '_blank').focus();
      })
    }
  }
}
</script>

<style scoped>

</style>