<template>
  <v-card>
    <v-card-title>Выдать доступ</v-card-title>
    <v-card-text>
      <BaseUserForm
          v-model="model"
          :errors="errors"
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
import BaseUserForm from "@/components/User/BaseUserForm";
import axios from "@/plugins/axios";

export default {
  name: "CreateUserDialog",
  components: {BaseUserForm},
  props: ['customerId', 'value'],
  data() {
    return {
      model: this.value,
      errors: {},
      modelName: 'user',
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
      axios.post(`${this.modelName}s`, {
        ...this.model,
        customer_id: this.customerId,
        permission:['orders'],
      }).then(body => {
        this.$emit("created", body[this.modelName]);
        this.$emit("close");
      }).catch(err => {
        this.errors = err.body.errors;
      })
    },
    update() {
      axios.put(`${this.modelName}s/${this.model.id}`, {
        ...this.model,
        permission:['orders'],
      }).then(body => {
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