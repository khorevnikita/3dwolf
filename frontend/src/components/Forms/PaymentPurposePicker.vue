<template>
  <v-autocomplete
      label="Цель платежа"
      v-model="payment_purpose_id"
      :items="paymentPurposes"
      :loading="isLoading"
      :search-input.sync="search"
      item-value="id"
      item-text="name"
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
      {{ item.name }}
    </template>
  </v-autocomplete>
</template>

<script>
import axios from "@/plugins/axios";

export default {
  name: "PaymentPurposePicker",
  props: ['value', 'error', 'dense', 'readonly'],
  data() {
    return {
      payment_purpose_id: this.value,
      paymentPurposes: [],
      isLoading: false,
      search: ''
    }
  },
  watch: {
    search(oldV, newV) {
      if (!newV) return;
      this.getPaymentPurposes()
    },
    payment_purpose_id: function (purposeId) {
      this.$emit('input', purposeId);
      const purpose = this.paymentPurposes.find(c => c.id === purposeId);
      if (purpose) {
        this.$emit('selected', purpose);
      }
    }
  },
  created() {
    this.getPaymentPurposes();
  },
  methods: {
    getPaymentPurposes() {
      if (this.isLoading) return;
      this.isLoading = true;
      axios.get(`payment-purposes?search=${this.search ? this.search : ''}&field=${this.payment_purpose_id ? this.payment_purpose_id : ''}`).then(body => {
        this.paymentPurposes = body.paymentPurposes;
        this.isLoading = false;
      })
    },
  }
}
</script>

<style scoped>

</style>