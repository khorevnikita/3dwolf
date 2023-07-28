<template>
  <v-row class="mx-3 mt-5">
    <v-col cols="12" md="7" v-if="isModerator">
      <div class="d-flex justify-space-between">
        <h6 class="text-h6">Изменить профиль</h6>
        <v-btn
            v-if="!user.tg_channel_id"
            color="info"
            text
            target="_blank"
            :href="`https://t.me/${tgUsername}?start=user-${user.id}`">
          Подключить Telegram
        </v-btn>
      </div>
      <BaseUserForm
          v-if="profile"
          v-model="profile"
          :errors="errors"
      />
      <v-btn color="primary" @click="setProfile()">Сохранить</v-btn>
    </v-col>
    <v-col v-else-if="customer" cols="12" md="7">
      <v-list>
        <v-list-item>
          <v-list-item-content>
            <v-list-item-subtitle>Имя</v-list-item-subtitle>
            <v-list-item-title>{{ customer.surname }} {{ customer.name }} {{ customer.father_name }}</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-list-item>
          <v-list-item-content>
            <v-list-item-subtitle>E-mail</v-list-item-subtitle>
            <v-list-item-title>{{ user.email }}</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-list-item>
          <v-list-item-content>
            <v-list-item-subtitle>Телефон</v-list-item-subtitle>
            <v-list-item-title>{{ customer.phone }}</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-list-item>
          <v-list-item-content>
            <v-list-item-subtitle>Telegram</v-list-item-subtitle>
            <v-list-item-title>{{ customer.telegram }}</v-list-item-title>
          </v-list-item-content>
        </v-list-item>


      </v-list>
    </v-col>
    <v-col cols="12" md="7">
      <h6 class="text-h6 mt-5">Изменить пароль</h6>
      <v-text-field
          type="password"
          label="Старый пароль"
          v-model="credentials.old_password"
          :error-messages="errors.old_password"
          :error-count="1"
          :error="!!errors.old_password"
      />
      <v-text-field
          type="password"
          label="Новый пароль"
          v-model="credentials.password"
          :error-messages="errors.password"
          :error-count="1"
          :error="!!errors.password"
      />
      <v-text-field
          type="password"
          label="Подтвердите пароль"
          v-model="credentials.password_confirmation"
          :error-messages="errors.password_confirmation"
          :error-count="1"
          :error="!!errors.password_confirmation"
      />
      <v-btn color="primary" @click="setPassword()">Сохранить</v-btn>
    </v-col>
  </v-row>
</template>

<script>
import {mapGetters} from "vuex";
import BaseUserForm from "@/components/User/BaseUserForm";
import axios from "@/plugins/axios";
import Swal from "sweetalert2-khonik";

export default {
  name: "ProfileView",
  components: {BaseUserForm},
  data() {
    return {
      profile: this.user,
      credentials: {
        old_password: "",
        password: "",
        password_confirmation: ""
      },
      errors: {},
      tgUsername: process.env.VUE_APP_TG_USERNAME,
    }
  },
  computed: {
    ...mapGetters(['user', 'isModerator']),
    customer() {
      return this.user?.customer;
    }
  },
  created() {
    this.profile = this.user;
  },
  methods: {
    setProfile() {
      this.errors = {};
      axios.post(`auth/profile`, this.profile).then(() => {
        Swal.fire("Данные сохранены")
      }).catch(err => {
        this.errors = err.body.errors;
      })
    },
    setPassword() {
      this.errors = {};
      axios.post(`auth/set-password`, this.credentials).then(() => {
        Swal.fire("Пароль изменён")
      }).catch(err => {
        this.errors = err.body.errors;
      })
    }
  }
}
</script>

<style scoped>

</style>