<template>
  <v-row class="mx-3 mt-5">
    <v-col cols="12">
      <div class="d-flex align-items-center">
        <div class="text-h6">Цели платежей</div>
        <v-spacer/>
        <v-btn small @click="create()" color="primary">Добавить</v-btn>
      </div>
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
                  @keydown.enter="replaceRoute"
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
        <template v-slot:[`item.name`]="{item}">
          <v-icon v-bind:style="{'color': item.color}">mdi-circle</v-icon>&nbsp;
          {{ item.name }}
        </template>
        <template v-slot:[`item.actions`]="{item}">
          <v-btn color="warning" icon @click="edit(item)">
            <v-icon>mdi-pencil</v-icon>
          </v-btn>
          <v-btn color="error" icon @click="destroy(item)">
            <v-icon>mdi-delete</v-icon>
          </v-btn>
        </template>
      </v-data-table>
    </v-col>

    <v-dialog v-model="editDialog" max-width="500">
      <PaymentPurposeEditor
          v-if="editDialog"
          @close="editDialog=false"
          v-model="editItem"
          @created="onCreated"
          @updated="onUpdated"
      />
    </v-dialog>
  </v-row>
</template>

<script>

import ResourceComponentHelper from "@/mixins/ResourceComponentHelper";
import PaymentPurposeEditor from "@/components/PaymentPurpose/PaymentPurposeEditor";

export default {
  name: "DeliveryAddresses",
  components: {PaymentPurposeEditor},
  mixins: [ResourceComponentHelper],
  data() {
    return {
      headers: [
        {text: "Название", value: "name", sortable: false},
        //  {text: "Цвет", value: "color", sortable: false},
        {text: "", value: "actions", sortable: false},
      ],
      resourceKey: "paymentPurposes",
      resourceApiRoute: `payment-purposes`,
      vehicleGroups: [],
      deleteSwalTitle: `Безвозвратно удалить цель?`,
    }
  },
}
</script>

<style scoped>

</style>