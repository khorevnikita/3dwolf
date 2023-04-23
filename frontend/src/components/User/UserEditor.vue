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

      <v-list-item-group v-model="model.permission" multiple>
        <v-list-item :value="permType.type" v-for="permType in permissionTypes" :key="permType.type">
          <template v-slot:default="{ active }">
            <v-list-item-action>
              <v-checkbox :input-value="active"/>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>{{ permType['title'] }}</v-list-item-title>
            </v-list-item-content>
          </template>
        </v-list-item>
      </v-list-item-group>
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
      errors: {},

      permissionTypes: [
        {title: 'Сотрудники', type: 'users'},
        {title: 'Клиенты', type: 'customers'},
        {title: 'Материалы', type: 'materials'},
        {title: 'Производители', type: 'manufacturers'},
        {title: 'Склад', type: 'parts'},
        {title: 'Счета', type: 'accounts'},
        {title: 'Наряд-заказы', type: 'orders'},
        {title: 'Договора', type: 'contracts'},
        {title: 'Деньги', type: 'payments'},
        {title: 'Сметы', type: 'estimates'},
        {title: 'Рассылки', type: 'newsletters'},
      ],
    }
  },
  created() {
    /*if (!this.model.permission) {
      this.$set(this.model, 'permission', []);
      this.permissionTypes.forEach(perm => {
        this.$set(this.model.permission, perm.type, false);
      })
    }*/
  },
  watch: {
    model: {
      handler() {
        console.log(this.model);
      }, deep: true
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