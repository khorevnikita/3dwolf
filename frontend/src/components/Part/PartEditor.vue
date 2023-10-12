<template>
  <v-card>
    <v-card-title>Редактирование позиции</v-card-title>
    <v-card-text>
      <v-text-field
          label="Произв. номер"
          v-model="model.prod_number"
          :error-messages="errors.prod_number"
          :error-count="1"
          :error="!!errors.prod_number"
          @change="onProdNumberChanged"
      />
      <v-select
          label="Производитель"
          v-model="model.manufacturer_id"
          :error-messages="errors.manufacturer_id"
          :error-count="1"
          :error="!!errors.manufacturer_id"
          :items="manufacturers"
          item-value="id"
          item-text="name"
      />

      <v-select
          label="Материал"
          v-model="model.material_id"
          :error-messages="errors.material_id"
          :error-count="1"
          :error="!!errors.material_id"
          :items="materials"
          item-value="id"
          item-text="name"
      />

      <v-text-field
          label="Цвет"
          v-model="model.color"
          :error-messages="errors.color"
          :error-count="1"
          :error="!!errors.color"
      />

      <v-text-field
          label="Вес"
          v-model="model.weight"
          :error-messages="errors.weight"
          :error-count="1"
          :error="!!errors.weight"
      />

      <v-text-field
          label="Цена"
          v-model="model.price"
          :error-messages="errors.price"
          :error-count="1"
          :error="!!errors.price"
      />

      <v-text-field
          label="Внутр. номер"
          v-model="model.inv_number"
          :error-messages="errors.inv_number"
          :error-count="1"
          :error="!!errors.inv_number"
          :disabled="fillByMask"
      />

      <v-text-field
          v-if="fillByMask && !model.id"
          type="number"
          label="Кол-во"
          v-model="model.count"
          :error-messages="errors.count"
          :error-count="1"
          :error="!!errors.count"/>


      <v-menu
          ref="menu"
          v-model="menu"
          :close-on-content-click="false"
          :return-value.sync="model.bought_at"
          transition="scale-transition"
          offset-y
          min-width="auto"
      >
        <template v-slot:activator="{ on, attrs }">
          <v-text-field
              v-model="model.bought_at"
              label="Дата покупки"
              readonly
              v-bind="attrs"
              v-on="on"
              :error-messages="errors.bought_at"
              :error-count="1"
              :error="!!errors.bought_at"
          ></v-text-field>
        </template>
        <v-date-picker
            v-model="model.bought_at"
            no-title
            scrollable
            :locale="'ru'"
        >
          <v-spacer></v-spacer>
          <v-btn
              text
              color="primary"
              @click="menu = false"
          >
            Отменить
          </v-btn>
          <v-btn
              text
              color="primary"
              @click="$refs.menu.save(model.bought_at)"
          >
            OK
          </v-btn>
        </v-date-picker>
      </v-menu>

      <v-select
          label="Статус"
          v-model="model.status"
          :error-messages="errors.status"
          :error-count="1"
          :error="!!errors.status"
          :items="[
              {value:'new',text:'Новая'},
              {value:'opened',text:'Вскрытая'},
              {value:'ended',text:'Закончилась'},
          ]"
          item-value="value"
          item-text="text"
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
  name: "PartEditor",
  props: ['value'],
  data() {
    return {
      menu: false,
      model: this.value,
      modelName: 'part',
      errors: {},
      manufacturers: [],
      materials: [],
      fillByMask: false,
    }
  },
  created() {
    if (!this.model.status) {
      this.model.status = "new";
    }
    this.getManufacturers();
    this.getMaterials();
  },
  methods: {
    async onProdNumberChanged() {
      await this.tryToAutocomplete()
      await this.checkMask();
    },
    async checkMask() {
      const {prodNumberMasks} = await axios.get(`prod-number-masks?prod_number=${this.model.prod_number}`)
      this.fillByMask = prodNumberMasks.length > 0;
      if (this.fillByMask) {
        const mask = prodNumberMasks[0];
        this.model.inv_number = mask.mask;
      }
    },
    async tryToAutocomplete() {
      if (this.model.id) return;
      const {parts} = await axios.get(`parts?prod_number=${this.model.prod_number}&take=1`)
      if (parts.length > 0) {
        const part = parts[0];
        this.model = {
          ...this.model,
          prod_number: part.prod_number,
          manufacturer_id: part.manufacturer_id,
          material_id: part.material_id,
          color: part.color,
          weight: part.weight,
          price: part.price,
          inv_number: undefined,
          count: undefined
        }
      } else {
        this.model = {
          ...this.model,
          manufacturer_id: undefined,
          material_id: undefined,
          color: undefined,
          weight: undefined,
          price: undefined,
          inv_number: undefined,
          count: undefined
        }
      }
    },
    getManufacturers() {
      axios.get('manufacturers').then(body => {
        this.manufacturers = body.manufacturers;
      })
    },
    getMaterials() {
      axios.get('materials').then(body => {
        this.materials = body.materials;
      })
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
      axios.post(`${this.modelName}s`, this.model).then(body => {
        if (body.part) {
          this.$emit("created", body.part);
        } else if (body.parts) {
          this.$emit("multiple_created", body.parts);
        }

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