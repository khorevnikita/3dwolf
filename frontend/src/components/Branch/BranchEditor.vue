<template>
  <v-card>
    <v-card-title>Редактирование филиала</v-card-title>
    <v-card-text>
      <v-text-field
          label="Название"
          v-model="model.name"
          :error-messages="errors.name"
          :error-count="1"
          :error="!!errors.name"
      />
      <v-text-field
          label="Ответственный"
          v-model="model.responsible_person"
          :error-messages="errors.responsible_person"
          :error-count="1"
          :error="!!errors.responsible_person"
      />
      <v-text-field
          label="Телефон"
          v-model="model.phone"
          :error-messages="errors.phone"
          :error-count="1"
          :error="!!errors.phone"
          v-mask="'+7 (###) ###-##-##'"
      />
      <v-textarea
          label="Адрес"
          v-model="model.address"
          :error-messages="errors.address"
          :error-count="1"
          :error="!!errors.address"
      />
      <v-textarea
          label="Часы работы"
          v-model="model.working_hours"
          :error-messages="errors.working_hours"
          :error-count="1"
          :error="!!errors.working_hours"
      />
      <v-switch label="По умолчанию" v-model="model.is_default"/>
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
  name: "BranchEditor",
  props: ['value'],
  data() {
    return {
      model: this.value,
      modelName: 'branch',
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
      axios.post(`${this.modelName}es`, this.model).then(body => {
        this.$emit("created", body[this.modelName]);
        this.$emit("close");
      }).catch(err => {
        this.errors = err.body.errors;
      })
    },
    update() {
      axios.put(`${this.modelName}es/${this.model.id}`, this.model).then(body => {
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