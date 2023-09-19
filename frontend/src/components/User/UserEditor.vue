<template>
  <v-card>
    <v-card-title>Редактирование пользователя</v-card-title>
    <v-card-text>
      <BaseUserForm
          v-model="model"
          :errors="errors"
      />

      <v-text-field
          label="Баланс"
          v-model="model.balance"
          :error-messages="errors.balance"
          :error-count="1"
          :error="!!errors.balance"
      />

      <v-select
          label="Доступ"
          v-model="model.access_level"
          :error-messages="errors.access_level"
          :error-count="1"
          :error="!!errors.access_level"
          :items="accessLevels"
          item-value="key"
          item-text="value"
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
import BaseUserForm from "@/components/User/BaseUserForm";
import {mapGetters} from "vuex";

export default {
  name: "UserEditor",
  components: {BaseUserForm},
  props: ['value'],
  data() {
    return {
      model: this.value,
      modelName: 'user',
      errors: {},
    }
  },
  computed: {
    ...mapGetters(['permissionTypes', 'accessLevels'])
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