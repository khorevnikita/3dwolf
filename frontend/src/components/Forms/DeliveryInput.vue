<template>
  <div>
    <v-select
        label="Выберите пункт доставки"
        :items="deliveryOptions"
        item-text="name"
        item-value="id"
        v-model="deliveryAddress"
        return-object
        @change="onSelectAddress"
    />
    <v-textarea
        label="Адрес доставки"
        v-model="input"
        :error-messages="error"
        :error-count="1"
        :error="!!error"
    />
  </div>
</template>

<script>
import axios from "@/plugins/axios";

export default {
  name: "DeliveryInput",
  props: ['value', 'error'],
  data() {
    return {
      input: this.value,
      deliveryAddress: undefined,
      deliveryAddresses: [],
      emptyAddress: {id: 0, name: "Другое", text: ""}
    }
  },
  created() {
    this.getAddresses();
  },
  watch: {
    input() {
      this.$emit("input", this.input)
    }
  },
  computed: {
    deliveryOptions() {
      return [
        ...this.deliveryAddresses,
        this.emptyAddress
      ];
    },
  },
  methods: {
    getAddresses() {
      axios.get(`delivery-addresses`).then(({deliveryAddresses}) => {
        this.deliveryAddresses = deliveryAddresses;
        this.$nextTick(() => {
          if (this.input) {
            const selected = this.deliveryAddresses.find(a => a.text === this.input);
            this.deliveryAddress = selected ? selected : this.emptyAddress
          }
        })
      })
    },
    onSelectAddress() {
      if (this.deliveryAddress) {
        this.input = this.deliveryAddress.text;
      }
    }
  }
}
</script>

<style scoped>

</style>