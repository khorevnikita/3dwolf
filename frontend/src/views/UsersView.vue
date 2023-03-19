<template>
  <v-row class="mx-3 mt-5">
    <v-col cols="12">
      <div class="d-flex align-items-center">
        <div class="text-h6">Пользователи</div>
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
        <template v-slot:[`item.balance`]="{item}">
          {{ formatPrice(item.balance) }}
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
      <UserEditor
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
import UserEditor from "@/components/User/UserEditor";
import ResourceComponentHelper from "@/mixins/ResourceComponentHelper";

export default {
  name: "UsersView",
  components: {UserEditor},
  mixins: [ResourceComponentHelper],
  data() {
    return {
      headers: [
        {text: "ID", value: "id", sortable: false},
        {text: "Фамилия", value: "surname", sortable: false},
        {text: "Имя", value: "name", sortable: false},
        {text: "E-mail", value: "email", sortable: false},
        {text: "Баланс", value: "balance", sortable: false},
        {text: "", value: "actions", sortable: false},
      ],
      resourceKey: "users",
      resourceApiRoute: `users`,
      deleteSwalTitle: `Безвозвратно удалить пользователя?`,
    }
  },
}
</script>

<style scoped>

</style>