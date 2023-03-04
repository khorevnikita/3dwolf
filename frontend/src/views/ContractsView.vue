<template>
  <v-row class="mx-3 mt-5">
    <v-col cols="12">
      <div class="text-h6">Договора</div>
    </v-col>
    <v-col cols="12">
      <v-card>
        <v-card-title>Фильтр</v-card-title>
        <v-card-text>
          <v-row>
            <v-col cols="12" md="4">
              <v-text-field
                  label="Поиск"
                  hide-details
                  v-model="query.search"
              />
            </v-col>
          </v-row>
        </v-card-text>
        <v-card-actions>
          <v-btn color="primary" @click="replaceRoute">Найти</v-btn>
        </v-card-actions>
      </v-card>
    </v-col>
    <v-col cols="12">
      <v-data-table
          :headers="headers"
          :items="items"
          :options.sync="options"
          :server-items-length="totalItems"
          :loading="loading"
          class="elevation-1 mt-3"
      >
        <template v-slot:[`item.actions`]="{item}">
          <v-btn color="error" icon @click="destroy(item)">
            <v-icon>mdi-delete</v-icon>
          </v-btn>
        </template>
      </v-data-table>
    </v-col>
  </v-row>
</template>

<script>

import ResourceComponentHelper from "@/mixins/ResourceComponentHelper";

export default {
  name: "ContractsView",
  mixins: [ResourceComponentHelper],
  data() {
    return {
      headers: [
        {text: "ID", value: "id", sortable: true},
        {text: "Номер", value: "number", sortable: true},
        {text: "Дата", value: "date", sortable: true},
        {text: "Статус", value: "status", sortable: true},
        {text: "Сумма", value: "amount", sortable: true},
        {text: "Клиент", value: "customer_id", sortable: false},
        {text: "", value: "actions", sortable: false},
      ],
      resourceKey: "contracts",
      resourceApiRoute: `contracts`,
      vehicleGroups: [],
      deleteSwalTitle: `Безвозвратно удалить договор?`,
    }
  },
}
</script>

<style scoped>

</style>