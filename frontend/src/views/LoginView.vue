<template>
  <v-card max-width="600" class="mx-auto my-10">
    <v-card-title>Авторизация</v-card-title>
    <v-card-text>
      <v-text-field
          label="E-mail"
          v-model="credentials.email"
          :error="!!errors.email"
          :error-count="errors.email?.length"
          :error-messages="errors.email"
      />
      <v-text-field
          label="Пароль"
          type="password"
          v-model="credentials.password"
          :error="!!errors.password"
          :error-count="errors.password?.length"
          :error-messages="errors.password"
      />
    </v-card-text>
    <v-card-actions>
      <v-btn color="primary" @click="signIn()">Войти</v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
import axios from "@/plugins/axios";

export default {
  name: "LoginView",
  data() {
    return {
      credentials: {
        email: '',
        password: ''
      },
      errors: {}
    }
  },
  methods: {
    signIn() {
      this.errors = {};
      axios.post(`auth/login`, this.credentials).then((body) => {
        this.$store.commit('setToken', body.access_token);
        this.$nextTick(() => {
          this.$router.push('/')
        })
      }).catch(({body}) => {
        this.errors = body.errors;
      })
    }
  }
}
</script>

<style scoped>

</style>