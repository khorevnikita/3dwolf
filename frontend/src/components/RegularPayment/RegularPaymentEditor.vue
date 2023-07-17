<template>
  <v-card>
    <v-card-title>Редактирование платежа</v-card-title>
    <v-card-text>

      <v-text-field
          label="Дата платежа"
          v-model="date"
          :error-messages="errors.schedule"
          :error-count="1"
          :error="!!errors.schedule"
          type="number"
          :rules="dateRules"
      />

      <v-text-field
          label="Кому"
          v-model="model.recipient"
          :error-messages="errors.recipient"
          :error-count="1"
          :error="!!errors.recipient"
      />
      <UserPicker
          lang="Сотрудник"
          v-model="model.user_id"
          :error="errors.user_id"
      />
      <v-text-field
          type="number"
          label="Сумма"
          v-model="model.amount"
          :error-messages="errors.amount"
          :error-count="1"
          :error="!!errors.amount"
      />
      <v-textarea
          label="Описание"
          v-model="model.description"
          :error-messages="errors.description"
          :error-count="1"
          :error="!!errors.description"
      />

    </v-card-text>
    <v-card-actions>
      <v-btn v-if="modal" text @click="$emit('close')">Закрыть</v-btn>
      <v-spacer/>
      <v-btn color="primary" @click="save()">Сохранить</v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
import axios from "@/plugins/axios";
import Swal from "sweetalert2-khonik";
import UserPicker from "@/components/Forms/UserPicker";

export default {
  name: "RegularPaymentEditor",
  components: {UserPicker},
  props: ['value', 'modal'],
  data() {
    return {
      model: this.value,
      modelName: 'regularPayment',
      errors: {},
      date: "",
      dateRules: [
        value => !!value || 'Обязательно для заполнения',
        value => Number(value) ? true : 'Должно быть числом',
        value => Number(value) > 0 && Number(value) <= 28 ? true : 'Не позже 28 числа каждого месяца',
      ]
    }
  },
  created() {
    if (this.model.schedule) {
      const parts = this.model.schedule.split(" ");
      this.date = parts[2];
    }
  },
  watch: {
    date() {
      const dayOfMonth = Number(this.date);
      this.model.schedule = `0 9 ${dayOfMonth} * *`
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
      axios.post(`regular-payments`, this.model).then(body => {
        this.$emit("created", body[this.modelName]);
        this.$emit("close");
        if (!this.modal) {
          Swal.fire('Данные сохранены');
        }
      }).catch(err => {
        this.errors = err.body.errors;
      })
    },
    update() {
      axios.put(`regular-payments/${this.model.id}`, this.model).then(body => {
        this.$emit("updated", body[this.modelName]);
        this.$emit("close");
        if (!this.modal) {
          Swal.fire('Данные сохранены');
        }
      }).catch(err => {
        this.errors = err.body.errors;
      })
    }
  }
}
</script>

<style scoped>

</style>