<template>
  <v-card>
    <v-card-title>Редактирование клиента</v-card-title>
    <v-card-text>
      <v-row>
        <v-col cols="12" md="4">
          <v-text-field
              label="Фамилия"
              v-model="model.surname"
              :error-messages="errors.surname"
              :error-count="1"
              :error="!!errors.surname"
          />
        </v-col>
        <v-col cols="12" md="4">
          <v-text-field
              label="Имя"
              v-model="model.name"
              :error-messages="errors.name"
              :error-count="1"
              :error="!!errors.name"
          />
        </v-col>
        <v-col cols="12" md="4">
          <v-text-field
              label="Отчество"
              v-model="model.father_name"
              :error-messages="errors.father_name"
              :error-count="1"
              :error="!!errors.father_name"
          />
        </v-col>
        <v-col cols="12" md="4">
          <v-text-field
              label="Телефон"
              v-model="model.phone"
              :error-messages="errors.phone"
              :error-count="1"
              :error="!!errors.phone"
              v-mask="'+7 (###) ###-##-##'"
          />
        </v-col>
        <v-col cols="12" md="4">
          <v-text-field
              label="E-mail"
              v-model="model.email"
              :error-messages="errors.email"
              :error-count="1"
              :error="!!errors.email"
          />
        </v-col>
        <v-col cols="12" md="4">
          <v-text-field
              label="Telegram"
              v-model="model.telegram"
              :error-messages="errors.telegram"
              :error-count="1"
              :error="!!errors.telegram"
          />
        </v-col>
      </v-row>

      <v-select
          label="Тип"
          v-model="model.type"
          :items="[
              {key:'individual',value:'Физ. лицо'},
              {key:'entity',value:'Юр. лицо'},
          ]"
          item-text="value"
          item-value="key"
          :error-messages="errors.type"
          :error-count="1"
          :error="!!errors.type"
      />
      <v-select
          v-if="model.type==='entity'"
          label="Тип юр. лица"
          v-model="model.entity_type"
          :items="[
              {key:'self_employed',value:'ИП'},
              {key:'company',value:'ООО'},
          ]"
          item-text="value"
          item-value="key"
          :error-messages="errors.entity_type"
          :error-count="1"
          :error="!!errors.entity_type"
      />

      <v-row v-if="model.type==='entity'">
        <v-col cols="12">
          <v-text-field
              label="Название"
              v-model="model.title"
              :error-messages="errors.title"
              :error-count="1"
              :error="!!errors.title"
          />
        </v-col>
        <v-col cols="12" md="6">
          <v-text-field
              label="ИНН"
              v-model="model.inn"
              :error-messages="errors.inn"
              :error-count="1"
              :error="!!errors.inn"
          />
        </v-col>
        <v-col cols="12" md="6">
          <v-text-field
              label="ОГРН (-ИП)"
              v-model="model.ogrn"
              :error-messages="errors.ogrn"
              :error-count="1"
              :error="!!errors.ogrn"
          />
        </v-col>
        <v-col cols="12" md="6">
          <v-text-field
              label="ОКПО"
              v-model="model.okpo"
              :error-messages="errors.okpo"
              :error-count="1"
              :error="!!errors.okpo"
          />
        </v-col>
        <v-col cols="12" md="6">
          <v-text-field
              label="ОКВЭД"
              v-model="model.okved"
              :error-messages="errors.okved"
              :error-count="1"
              :error="!!errors.okved"
          />
        </v-col>
        <v-col cols="12" v-if="model.entity_type==='self_employed'">
          <v-textarea
              label="Адрес"
              v-model="model.address"
              :error-messages="errors.address"
              :error-count="1"
              :error="!!errors.address"
          />
        </v-col>
        <v-col cols="12" md="6" v-if="model.entity_type==='company'">
          <v-text-field
              label="КПП"
              v-model="model.kpp"
              :error-messages="errors.kpp"
              :error-count="1"
              :error="!!errors.kpp"
          />
        </v-col>
        <v-col cols="12" md="6" v-if="model.entity_type==='company'">
          <v-text-field
              label="Директор"
              v-model="model.ceo"
              :error-messages="errors.ceo"
              :error-count="1"
              :error="!!errors.ceo"
          />
        </v-col>
      </v-row>

      <v-row v-if="model.type==='entity'">
        <v-col cols="12" md="6">
          <v-text-field
              label="Р/С"
              v-model="model.rs"
              :error-messages="errors.rs"
              :error-count="1"
              :error="!!errors.rs"
          />
        </v-col>
        <v-col cols="12" md="6">
          <v-text-field
              label="Банк"
              v-model="model.bank"
              :error-messages="errors.bank"
              :error-count="1"
              :error="!!errors.bank"
          />
        </v-col>
        <v-col cols="12" md="6">
          <v-text-field
              label="БИК"
              v-model="model.bik"
              :error-messages="errors.bik"
              :error-count="1"
              :error="!!errors.bik"
          />
        </v-col>
        <v-col cols="12" md="6">
          <v-text-field
              label="К/С"
              v-model="model.ks"
              :error-messages="errors.ks"
              :error-count="1"
              :error="!!errors.ks"
          />
        </v-col>
      </v-row>
      <v-select
          label="Источник"
          v-model="model.source"
          :error-messages="errors.source"
          :error-count="1"
          :error="!!errors.source"
          :items="[
              {value:'site',text:'Сайт'},
              {value:'avito',text:'Авито'},
          ]"
          item-text="text"
          item-value="value"
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
      <v-btn v-if="modal" text @click="$emit('close')">Закрыть</v-btn>
      <v-spacer/>
      <v-btn color="primary" @click="save()">Сохранить</v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
import axios from "@/plugins/axios";

export default {
  name: "CustomerEditor",
  props: ['value','modal'],
  data() {
    return {
      model: this.value,
      modelName: 'customer',
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