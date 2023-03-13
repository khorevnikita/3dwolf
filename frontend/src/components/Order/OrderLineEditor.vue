<template>
  <v-card>
    <v-card-title>Редактирование позиции</v-card-title>
    <v-card-text>
      <v-text-field
          label="Номер"
          v-model="model.number"
          :error-messages="errors.number"
          :error-count="1"
          :error="!!errors.number"
      />
      <v-autocomplete
          label="Деталь"
          v-model="model.part_id"
          :items="parts"
          :loading="isLoadingParts"
          :search-input.sync="searchPart"
          item-value="id"
          item-text="inv_number"
          :error-messages="errors.part_id"
          :error-count="1"
          :error="!!errors.part_id"
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
          type="number"
          label="Вес"
          v-model="model.weight"
          :error-messages="errors.weight"
          :error-count="1"
          :error="!!errors.weight"
      />
      <v-text-field
          type="number"
          label="Длительность печати"
          v-model="model.print_duration"
          :error-messages="errors.print_duration"
          :error-count="1"
          :error="!!errors.print_duration"
      />

      <v-text-field
          type="number"
          label="Кол-во"
          v-model="model.count"
          :error-messages="errors.count"
          :error-count="1"
          :error="!!errors.count"
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

export default {
  name: "OrderLineEditor",
  props: ['value', 'modal'],
  data() {
    return {
      model: this.value,
      modelName: 'orderLine',
      errors: {},
      menu: false,
      menu2: false,
      parts: [],
      isLoadingParts: false,
      searchPart: ''
    }
  },
  watch: {
    searchPart(oldV, newV) {
      if (!newV) return;
      this.getParts()
    }
  },
  created() {
    this.getParts();
  },
  methods: {
    getParts() {
      if (this.isLoadingParts) return;
      this.isLoadingParts = true;
      axios.get(`parts?search=${this.searchPart ? this.searchPart : ''}&field=${this.model.part_id ? this.model.part_id : ''}`)
          .then(body => {
            this.parts = body.parts;
            this.isLoadingParts = false;
          });
    },
    save() {
      this.errors = {};
      if (this.model.id) {
        this.update();
      } else {
        this.store();
      }
    },
    store() {
      axios.post(`orders/${this.$route.params.id}/order-lines`, this.model).then(body => {
        this.$emit("created", body[this.modelName]);
        this.$emit("close");
      }).catch(err => {
        this.errors = err.body.errors;
      })
    },
    update() {
      axios.put(`orders/${this.$route.params.id}/order-lines/${this.model.id}`, this.model).then(body => {
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