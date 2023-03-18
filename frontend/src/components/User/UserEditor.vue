<template>
  <v-card>
    <v-card-title>Редактирование пользователя</v-card-title>
    <v-card-text>
      <v-text-field
          label="Фамилия"
          v-model="model.surname"
          :error-messages="errors.surname"
          :error-count="1"
          :error="!!errors.surname"
      />
      <v-text-field
          label="Имя"
          v-model="model.name"
          :error-messages="errors.name"
          :error-count="1"
          :error="!!errors.name"
      />

      <v-text-field
          label="E-mail"
          v-model="model.email"
          :error-messages="errors.email"
          :error-count="1"
          :error="!!errors.email"
      />
      <v-text-field
          v-if="!model.id"
          type="password"
          label="Пароль"
          v-model="model.password"
          :error-messages="errors.password"
          :error-count="1"
          :error="!!errors.password"
      />
       <v-text-field
          v-if="!model.id"
          type="password"
          label="Подтвердите пароль"
          v-model="model.password_confirmation"
          :error-messages="errors.password_confirmation"
          :error-count="1"
          :error="!!errors.password_confirmation"
      />

      <v-text-field
          label="Баланс"
          v-model="model.balance"
          :error-messages="errors.balance"
          :error-count="1"
          :error="!!errors.balance"
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
  name: "UserEditor",
  props: ['value'],
  data() {
    return {
      model: this.value,
      modelName: 'user',
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