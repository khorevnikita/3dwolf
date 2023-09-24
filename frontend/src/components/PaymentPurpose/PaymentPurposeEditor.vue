<template>
  <v-card>
    <v-card-title>Редактирование цели платежей</v-card-title>
    <v-card-text>
      <v-text-field
          label="Название"
          v-model="model.name"
          :error-messages="errors.name"
          :error-count="1"
          :error="!!errors.name"
      />
      <v-color-picker
          v-model="model.color"
          type="hex"
      />
      <p class="error--text text-small" v-if="errors.color">{{ errors.color }}</p>
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
  name: "PaymentPurposeEditor",
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
      axios.post(`payment-purposes`, {
        name: this.model.name,
        color: this.model.color.hexa,
      }).then(body => {
        this.$emit("created", body.paymentPurpose);
        this.$emit("close");
      }).catch(err => {
        this.errors = err.body.errors;
      })
    },
    update() {
      axios.put(`payment-purposes/${this.model.id}`, this.model).then(body => {
        this.$emit("updated", body.paymentPurpose);
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