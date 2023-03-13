<template>
  <v-card>
    <v-card-title>Клиент</v-card-title>
    <v-card-text>
      <v-list>
        <v-list-item>
          <v-list-item-content>
            <v-list-item-subtitle>Название</v-list-item-subtitle>
            <v-list-item-title>{{customer.title}}</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-list-item>
          <v-list-item-content>
            <v-list-item-subtitle>Имя</v-list-item-subtitle>
            <v-list-item-title>{{ customer.name }} {{ customer.surname }}</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-list-item>
          <v-list-item-content>
            <v-list-item-subtitle>Контакт</v-list-item-subtitle>
            <v-list-item-title>{{ customer.email }} {{ customer.phone }}</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-card-text>
    <v-card-actions>
      <v-spacer/>
      <v-btn text :to="`/customers/${customer.id}`">Открыть профиль</v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
export default {
  name: "CustomerCard",
  props:['customer']
}
</script>

<style scoped>

</style>