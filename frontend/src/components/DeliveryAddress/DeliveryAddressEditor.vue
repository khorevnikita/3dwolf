<template>
  <v-card>
    <v-card-title>Редактирование материала</v-card-title>
    <v-card-text>
      <v-text-field
          label="Название"
          v-model="model.name"
          :error-messages="errors.name"
          :error-count="1"
          :error="!!errors.name"
      />
      <v-textarea
          label="Текст"
          v-model="model.text"
          :error-messages="errors.text"
          :error-count="1"
          :error="!!errors.text"
      />
    </v-card-text>
    <v-card-actions>
      <v-btn text @click="$emit('close')">Закрыть</v-btn>
      <v-spacer/>
      <v-btn color="primary" @click="save()">Сохранить</v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
import axios from "@/plugins/axios";

export default {
  name: "DeliveryAddressEditor",
  props: ['value'],
  data() {
    return {
      model: this.value,
      errors: {}
    }
  },
  methods: {
    save() {
      this.errors = {};
      if (this.model.id) {
        this.update();
      } else {
        this.store();
      }
    },
    store() {
      axios.post(`delivery-addresses`, this.model).then(body => {
        this.$emit("created", body.deliveryAddress);
        this.$emit("close");
      }).catch(err => {
        this.errors = err.body.errors;
      })
    },
    update() {
      axios.put(`delivery-addresses/${this.model.id}`, this.model).then(body => {
        this.$emit("updated", body.deliveryAddress);
        this.$emit("close");
      }).catch(err => {
        this.errors = err.body.errors;
      })
    }
  }
}
</script>

<style scoped>

</style>