<template>
  <v-row class="mx-3 mt-5">
    <v-col cols="12">
      <div class="d-flex align-items-center">
        <div class="text-h6">Филиалы</div>
        <v-spacer/>
        <v-btn small @click="create()" color="primary">Создать</v-btn>
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
          {{item.name}} <v-icon v-if="item.is_default">mdi-check</v-icon>
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
      <BranchEditor
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
import BranchEditor from "@/components/Branch/BranchEditor";
import ResourceComponentHelper from "@/mixins/ResourceComponentHelper";

export default {
  name: "BranchesView",
  components: {BranchEditor},
  mixins: [ResourceComponentHelper],
  data() {
    return {
      headers: [
        {text: "ID", value: "id", sortable: false},
        {text: "Название", value: "name", sortable: false},
        {text: "Ответственный", value: "responsible_person", sortable: false},
        {text: "Телефон", value: "phone", sortable: false},
        {text: "Адрес", value: "address", sortable: false},
        {text: "Часы работы", value: "working_hours", sortable: false},
        {text: "", value: "actions", sortable: false},
      ],
      resourceKey: "branches",
      resourceApiRoute: `branches`,
      deleteSwalTitle: `Безвозвратно удалить филиал?`,
    }
  },
}
</script>

<style scoped>

</style>