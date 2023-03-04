<template>
  <v-card>
    <v-card-title>Редактирование счета</v-card-title>
    <v-card-text>
      <v-text-field
          label="Название"
          v-model="model.name"
          :error-messages="errors.name"
          :error-count="1"
          :error="!!errors.name"
      />
      <v-text-field
          label="Баланс"
          v-model="model.balance"
          :error-messages="errors.balance"
          :error-count="1"
          :error="!!errors.balance"
      />
      <v-text-field
          label="Ожидаемый доход"
          v-model="model.expected_income"
          :error-messages="errors.expected_income"
          :error-count="1"
          :error="!!errors.expected_income"
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
  name: "AccountEditor",
  props: ['value'],
  data() {
    return {
      model: this.value,
      modelName: 'account',
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
      axios.post(`${this.modelName}s`, this.model).then(body => {
        this.$emit("created", body[this.modelName]);
        this.$emit("close");
      }).catch(err => {
        this.errors = err.body.errors;
      })
    },
    update() {
      axios.put(`${this.modelName}s/${this.model.id}`, this.model).then(body => {
        this.$emit("updated", body[this.modelName]);
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