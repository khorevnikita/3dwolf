<template>
  <v-card>
    <v-card-title>Платежи</v-card-title>
    <v-card-text>
      <v-alert type="info" v-if="payments.length===0">Платежей нет</v-alert>
    </v-card-text>
    <v-card-actions>
      <v-spacer/>
      <v-btn text>Добавить платёж</v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
export default {
  name: "PaymentCard",
  props:['payments']
}
</script>

<style scoped>

</style>