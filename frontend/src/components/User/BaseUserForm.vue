<template>
  <div>
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
        label="Телефон"
        v-model="model.phone"
        :error-messages="errors.phone"
        :error-count="1"
        :error="!!errors.phone"
        v-mask="'+7 (###) ###-##-##'"
    />
    <v-text-field
        label="Telegram"
        v-model="model.tg_username"
        :error-messages="errors.tg_username"
        :error-count="1"
        :error="!!errors.tg_username"
        :append-icon="model.tg_channel_id?'mdi-check':'mdi-radiobox-blank'"
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
  </div>
</template>

<script>
export default {
  name: "BaseUserForm",
  props: ['value', 'errors'],
  data() {
    return {
      model: this.value,
    }
  }
}
</script>

<style scoped>

</style>