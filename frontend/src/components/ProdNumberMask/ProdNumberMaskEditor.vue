<template>
  <v-card>
    <v-card-title>Редактирование маску</v-card-title>
    <v-card-text>
      <v-text-field
          label="Номер производителя"
          v-model="model.prod_number"
          :error-messages="errors.prod_number"
          :error-count="1"
          :error="!!errors.prod_number"
      />
      <v-text-field
          label="Маска"
          v-model="model.mask"
          :error-messages="errors.mask"
          :error-count="1"
          :error="!!errors.mask"
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
  name: "ProdNumberMaskEditor",
  props: ['value'],
  data() {
    return {
      model: this.value,
      modelName: 'prodNumberMask',
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
      axios.post(`prod-number-masks`, this.model).then(body => {
        this.$emit("created", body[this.modelName]);
        this.$emit("close");
      }).catch(err => {
        this.errors = err.body.errors;
      })
    },
    update() {
      axios.put(`prod-number-masks/${this.model.id}`, this.model).then(body => {
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