<template>
  <v-card>
    <v-card-title>Редактирование позиции в смете</v-card-title>
    <v-card-text>
      <v-text-field
          label="Обозначение"
          v-model="model.key"
          :error-messages="errors.key"
          :error-count="1"
          :error="!!errors.key"
      />

      <v-text-field
          label="Название"
          v-model="model.name"
          :error-messages="errors.name"
          :error-count="1"
          :error="!!errors.name"
      />
      <v-text-field
          label="Цена"
          v-model="model.price"
          :error-messages="errors.price"
          :error-count="1"
          :error="!!errors.price"
      />

      <v-text-field
          label="Вес"
          v-model="model.weight"
          :error-messages="errors.weight"
          :error-count="1"
          :error="!!errors.weight"
      />

      <v-text-field
          label="Кол-во"
          v-model="model.count"
          :error-messages="errors.count"
          :error-count="1"
          :error="!!errors.count"
      />

      <v-text-field
          label="Продолжительность печати"
          v-model="model.print_duration"
          :error-messages="errors.print_duration"
          :error-count="1"
          :error="!!errors.print_duration"
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
  name: "EstimateLineEditor",
  props: ['value', 'estimate_id'],
  data() {
    return {
      model: this.value,
      modelName: 'estimateLine',
      errors: {},
      menu: false,
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
      axios.post(`estimates/${this.estimate_id}/estimate-lines`, this.model).then(body => {
        this.$emit("created", body[this.modelName]);
        this.$emit("close");
      }).catch(err => {
        this.errors = err.body.errors;
      })
    },
    update() {
      axios.put(`estimates/${this.estimate_id}/estimate-lines/${this.model.id}`, this.model).then(body => {
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