<template>
  <v-card>
    <v-card-title v-if="modal">Редактирование наряд-заказа</v-card-title>
    <v-card-text>
      <DatePicker
          label="Дата"
          v-model="model.date"
          :error="errors.date"
      />
      <BranchPicker
          v-model="model.branch_id"
          :error="errors.branch_id"
          :dense="false"
      />
      <CustomerPicker
          v-model="model.customer_id"
          :error="errors.customer_id"
          @selected="onCustomerSelected"
          :dense="false"
      />
      <v-text-field
          label="Телефон"
          v-model="model.phone"
          :error-messages="errors.phone"
          :error-count="1"
          :error="!!errors.phone"
          v-mask="'+7 (###) ###-##-##'"
      />
      <DatePicker
          label="Дедлайн"
          v-model="model.deadline"
          :error="errors.deadline"
      />
      <v-select
          label="Статус"
          v-model="model.status"
          :error-messages="errors.status"
          :error-count="1"
          :error="!!errors.status"
          :items="orderStatuses"
          item-value="value"
          item-text="text"
      />
      <v-select
          label="Статус оплаты"
          v-model="model.payment_status"
          :error-messages="errors.payment_status"
          :error-count="1"
          :error="!!errors.payment_status"
          :items="paymentStatuses"
          item-value="value"
          item-text="text"
      />
      <DeliveryInput
          v-model="model.delivery_address"
          :error="errors.delivery_address"
          @onAddressId="id=>model.delivery_address_id=id"
          :selectedId="model.delivery_address_id"
      />
      <v-text-field
          label="Трек-номер"
          v-model="model.tk_link"
          :error-messages="errors.tk_link"
          :error-count="1"
          :error="!!errors.tk_link"
      />

      <v-textarea
          label="Комментарий к заказу"
          v-model="model.comment"
          :error-messages="errors.comment"
          :error-count="1"
          :error="!!errors.comment"
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
import CustomerPicker from "@/components/Forms/CustomerPicker";
import Swal from "sweetalert2-khonik";
import {orderStatuses, paymentStatuses} from "@/mixins/StatusHelper";
import BranchPicker from "@/components/Forms/BranchPicker";
import DatePicker from "@/components/Forms/DatePicker";
import DeliveryInput from "@/components/Forms/DeliveryInput";

export default {
  name: "OrderEditor",
  components: {DeliveryInput, DatePicker, BranchPicker, CustomerPicker},
  props: ['value', 'modal'],
  data() {
    return {
      model: {...this.value},
      modelName: 'order',
      errors: {},
      orderStatuses: orderStatuses,
      paymentStatuses: paymentStatuses,
    }
  },
  methods: {
    onCustomerSelected(customer) {
      this.model.phone = customer.phone;
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
        if (!this.modal) {
          Swal.fire('Данные сохранены');
        }
      }).catch(err => {
        this.errors = err.body.errors;
      })
    },
    update() {
      axios.put(`${this.modelName}s/${this.model.id}`, this.model).then(body => {
        this.$emit("updated", body[this.modelName]);
        this.$emit("close");
        if (!this.modal) {
          Swal.fire('Данные сохранены');
        }
      }).catch(err => {
        this.errors = err.body.errors;
      })
    }
  }
}
</script>

<style scoped>

</style>