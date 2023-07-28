<template>
  <v-card>
    <v-card-title>Редактирование задачи</v-card-title>
    <v-card-text>
      <v-text-field
          label="Название"
          v-model="model.name"
          :error-messages="errors.name"
          :error-count="1"
          :error="!!errors.name"
      />
      <v-textarea
          label="Описание"
          v-model="model.description"
          :error-messages="errors.description"
          :error-count="1"
          :error="!!errors.description"
      />
      <DateTimePicker
          label="Время"
          v-model="model.datetime"
          :error="errors.datetime"
      />
      <UserPicker
          lang="Ответственный"
          v-model="model.user_id"
          :error="errors.user_id"

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
import UserPicker from "@/components/Forms/UserPicker";
import DatePicker from "@/components/Forms/DatePicker";
import DateTimePicker from "@/components/Forms/DateTimePicker";

export default {
  name: "TaskEditor",
  components: {DateTimePicker, DatePicker, UserPicker},
  props: ['value'],
  data() {
    return {
      model: this.value,
      modelName: 'task',
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