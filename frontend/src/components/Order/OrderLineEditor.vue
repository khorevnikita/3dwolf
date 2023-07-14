<template>
  <v-card>
    <v-card-title>Редактирование позиции</v-card-title>
    <v-card-text>
      <!--<v-text-field
          label="Номер"
          v-model="model.number"
          :error-messages="errors.number"
          :error-count="1"
          :error="!!errors.number"
      />-->
      <v-autocomplete
          label="Катушка"
          v-model="model.part_id"
          :items="parts"
          :loading="isLoadingParts"
          :search-input.sync="searchPart"
          item-value="id"
          item-text="inv_number"
          :error-messages="errors.part_id"
          :error-count="1"
          :error="!!errors.part_id"
          :filter="()=>true"
      >
        <template #item="{item}">
          {{ item.inv_number }}. {{ `${item.material.name}, ${item.color} (${item.prod_number})` }}
        </template>
      </v-autocomplete>

      <v-text-field
          label="Название"
          v-model="model.name"
          :error-messages="errors.name"
          :error-count="1"
          :error="!!errors.name"
      />
      <v-text-field
          label="Заполнение"
          v-model="model.filling"
          :error-messages="errors.filling"
          :error-count="1"
          :error="!!errors.filling"
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
      <v-row>
        <v-col cols="4">
          <v-text-field
              type="number"
              label="Время, часы"
              v-model="printDuration.h"
          />
        </v-col>
        <v-col cols="4">
          <v-text-field
              type="number"
              max="59"
              label="Время, минуты"
              v-model="printDuration.m"
          />
        </v-col>
        <v-col cols="4">
          <v-text-field
              type="number"
              max="59"
              label="Время, секунды"
              v-model="printDuration.s"
          />
        </v-col>
      </v-row>

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
      searchPart: '',
      printDuration: {
        h: 0,
        m: 0,
        s: 0
      }
    }
  },
  watch: {
    searchPart(oldV, newV) {
      if (!newV) return;
      this.getParts()
    },
    printDuration: {
      handler() {
        this.model.print_duration = this.printHours * 3600 + this.printMinutes * 60 + this.printSeconds;
      }, deep: true
    }
  },
  computed: {
    printHours() {
      const h = Number(this.printDuration.h);
      if (!h) return 0;
      return h;
    },
    printMinutes() {
      const m = Number(this.printDuration.m);
      if (!m) return 0;
      return m;
    },
    printSeconds() {
      const s = Number(this.printDuration.s);
      if (!s) return 0;
      return s;
    },
  },
  created() {
    this.getParts();

    let totalSeconds = this.model.print_duration;
    this.printDuration.h = Math.floor(totalSeconds / 3600);
    totalSeconds %= 3600;
    this.printDuration.m = Math.floor(totalSeconds / 60);
    this.printDuration.s = totalSeconds % 60;
  },
  methods: {
    getParts() {
      if (this.isLoadingParts) return;
      this.isLoadingParts = true;
      axios.get(`parts?not_ended=1&search=${this.searchPart ? this.searchPart : ''}&field=${this.model.part_id ? this.model.part_id : ''}`)
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
      console.log(this.printDuration);
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