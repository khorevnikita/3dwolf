<template>
  <v-row class="mx-3 mt-5">
    <v-col cols="12">
      <div class="d-flex align-items-center">
        <div class="text-h6">Склад</div>
        <v-spacer/>
        <v-btn small @click="create()" color="primary">Добавить позицию</v-btn>
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
        <template #item="{item}">
          <tr v-bind:class="{'is-new':item.status==='new','is-opened':item.status==='opened','is-finished':item.status==='ended'}">
            <td>{{ item.id }}</td>
            <td>{{ moment(item.bought_at).format("DD.MM.YYYY") }}</td>
            <td>{{ item.inv_number }}</td>
            <td>{{ item.prod_number }}</td>
            <td>{{ item.manufacturer ? item.manufacturer.name : '-' }}</td>
            <td>{{ item.material ? item.material.name : '-' }}</td>
            <td>{{ item.color }}</td>
            <td>{{ item.weight }}</td>
            <td>{{ formatPrice(item.price) }}</td>
            <td>{{ statuses[item.status] }}</td>
            <td>
              <v-btn color="warning" icon @click="edit(item)">
                <v-icon>mdi-pencil</v-icon>
              </v-btn>
              <v-btn color="error" icon @click="destroy(item)">
                <v-icon>mdi-delete</v-icon>
              </v-btn>
            </td>
          </tr>
        </template>
      </v-data-table>
    </v-col>

    <v-dialog v-model="editDialog" max-width="500">
      <PartEditor
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
import PartEditor from "@/components/Part/PartEditor";

export default {
  name: "StockView",
  components: {PartEditor},
  mixins: [ResourceComponentHelper],
  data() {
    return {
      headers: [
        {text: "ID", value: "id", sortable: true},
        {text: "Дата покупки", value: "bought_at", sortable: true},
        {text: "Вн. номер", value: "inv_number", sortable: true},
        {text: "Пр. номер", value: "prod_number", sortable: true},
        {text: "Производитель", value: "manufacturer_id", sortable: false},
        {text: "Материал", value: "material_id", sortable: false},
        {text: "Цвет", value: "color", sortable: false},
        {text: "Вес", value: "weight", sortable: true},
        {text: "Цена", value: "price", sortable: true},
        {text: "Статус", value: "status", sortable: false},
        {text: "", value: "actions", sortable: false},
      ],
      resourceKey: "parts",
      resourceApiRoute: `parts`,
      deleteSwalTitle: `Безвозвратно удалить позицию?`,
      statuses: {
        new: "Новая",
        opened: "Вскрытая",
        ended: "Закончилась"
      }
    }
  },
}
</script>

<style>
.is-new {
  background: #dfffae;
}

.is-opened {
  background: #bbe8ff;
}

.is-finished {
  color: gray;
  text-decoration: line-through;
}
</style>