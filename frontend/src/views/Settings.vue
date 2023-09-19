<template>
  <div>
    <div class="text-h6">Настройки</div>

    <div class="text-subtitle-1 mt-3">
      <b>Договор</b>
    </div>
    <v-row>
      <v-col cols="12" md="6">
        <v-text-field
            label="Товарное имя"
            v-model="settings.brand_name"
            :error="!!errors.brand_name"
            :error-count="1"
            :error-messages="errors.brand_name"
        />

      </v-col>
      <v-col cols="12" md="6">
        <v-text-field
            label="Название юр. лица (коротко)"
            v-model="settings.legal_name"
            :error-messages="errors.legal_name"
            :error="!!errors.legal_name"
            :error-count="1"
        />

      </v-col>
      <v-col cols="12">
        <v-text-field
            label="Название юр. лица (полное)"
            v-model="settings.legal_full_name"
            :error-messages="errors.legal_full_name"
            :error="!!errors.legal_full_name"
            :error-count="1"
        />
      </v-col>
      <v-col cols="12" md="4">
        <v-text-field
            label="Основание договора"
            v-model="settings.legal_statement"
            :error-messages="errors.legal_statement"
            :error="!!errors.legal_statement"
            :error-count="1"
        />
      </v-col>
      <v-col cols="12" md="4">
        <v-text-field
            label="ОГРН/ОГРНИП"
            v-model="settings.ogrn"
            :error-messages="errors.ogrn"
            :error="!!errors.ogrn"
            :error-count="1"
        />
      </v-col>
      <v-col cols="12" md="4">
        <v-text-field
            label="ИНН"
            v-model="settings.inn"
            :error-messages="errors.inn"
            :error="!!errors.inn"
            :error-count="1"
        />
      </v-col>

      <v-col cols="12" md="4">
        <v-text-field
            label="Город"
            v-model="settings.city"
            :error-messages="errors.city"
            :error="!!errors.city"
            :error-count="1"
        />
      </v-col>
      <v-col cols="12" md="8">
        <v-text-field
            label="Адрес"
            v-model="settings.address"
            :error-messages="errors.address"
            :error="!!errors.address"
            :error-count="1"
        />
      </v-col>
    </v-row>

    <div class="text-subtitle-1 mt-3">
      <b>Банк</b>
    </div>
    <v-row>
      <v-col cols="12">
        <v-text-field
            label="Название банка"
            v-model="settings.bank"
            :error-messages="errors.bank"
            :error="!!errors.bank"
            :error-count="1"
        />
      </v-col>
      <v-col cols="12" md="4">
        <v-text-field
            label="Р/с"
            v-model="settings.rs"
            :error-messages="errors.rs"
            :error="!!errors.rs"
            :error-count="1"
        />
      </v-col>
      <v-col cols="12" md="4">
        <v-text-field
            label="К/с"
            v-model="settings.ks"
            :error-messages="errors.ks"
            :error="!!errors.ks"
            :error-count="1"
        />
      </v-col>
      <v-col cols="12" md="4">
        <v-text-field
            label="БИК"
            v-model="settings.bik"
            :error-messages="errors.bik"
            :error="!!errors.bik"
            :error-count="1"
        />
      </v-col>
    </v-row>

    <div class="text-subtitle-1 mt-3">
      <b>Контакты</b>
    </div>
    <v-row>
      <v-col cols="12" md="4">
        <v-text-field
            label="Телефон"
            v-model="settings.phone"
            :error-messages="errors.phone"
            :error="!!errors.phone"
            :error-count="1"
        />
      </v-col>
      <v-col cols="12" md="4">
        <v-text-field
            label="E-mail"
            v-model="settings.email"
            :error-messages="errors.email"
            :error="!!errors.email"
            :error-count="1"
        />
      </v-col>
      <v-col cols="12" md="4">
        <v-text-field
            label="Website"
            v-model="settings.website"
            :error-messages="errors.website"
            :error="!!errors.website"
            :error-count="1"
        />
      </v-col>
    </v-row>
    <div class="text-subtitle-1 mt-3">
      <b>Время отправки задачи</b>
    </div>
    <TimePicker
        v-model="settings.notification_time"
    />

    <v-btn color="primary" @click="setSettings()">Сохранить</v-btn>
  </div>
</template>

<script>
import axios from "@/plugins/axios";
import Swal from "sweetalert2-khonik";
import TimePicker from "@/components/Forms/TimePicker";

export default {
  name: "Settings",
  components: {TimePicker},
  data() {
    return {
      settings: {},
      errors: {}
    }
  },
  created() {
    this.getSettings();
  },
  methods: {
    getSettings() {
      axios.get(`settings`).then(({settings}) => {
        this.settings = settings;
      })
    },
    setSettings() {
      axios.post(`settings`, this.settings).then(() => {
        Swal.fire("Сохранено")
      }).catch(err => {
        this.errors = err.body.errors;
      })
    }
  }
}
</script>

<style scoped>

</style>