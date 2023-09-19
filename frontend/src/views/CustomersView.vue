<template>
  <v-row class="mx-3 mt-5">
    <v-col cols="12">
      <div class="d-flex align-items-center">
        <div class="text-h6">Клиенты</div>
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
                  dense
              />
            </v-col>
            <v-col cols="12" md="4">
              <v-select
                  label="Тип"
                  hide-details
                  v-model="query.type"
                  :items="[
                      {value:'',text:'Все'},
                      {value:'individual',text:'Физ. лицо'},
                      {value:'entity',text:'Юр. лицо'},
                  ]"
                  item-text="text"
                  item-value="value"
                  dense
              />
            </v-col>
            <v-col cols="12" md="4">
              <v-select
                  label="Тип ЮЛ"
                  hide-details
                  v-model="query.entity_type"
                  :items="[
                      {value:'',text:'Все'},
                      {value:'self_employed',text:'ИП'},
                      {value:'company',text:'ООО'},
                  ]"
                  item-text="text"
                  item-value="value"
                  dense
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
          <v-btn link :href="`/customers/${item.id}`" icon>
            <v-icon>mdi-eye</v-icon>
          </v-btn>
        </template>
        <template v-slot:[`item.surname`]="{item}">
          {{ item.surname }}
          <div style="font-size: 10px">{{item.user && item.user.last_activity_date ? moment(item.user.last_activity_date).format("HH:mm DD.MM.YYYY") : '-'}}</div>
        </template>
        <template v-slot:[`item.type`]="{item}">
          {{ item.type === 'individual' ? 'Физ. лицо' : 'Юр. лицо' }}
        </template>
        <template v-slot:[`item.entity_type`]="{item}">
          {{ item.entity_type === 'self_employed' ? 'ИП' : item.entity_type === 'company' ? 'ООО' : '-' }}
        </template>
        <template v-slot:[`item.title`]="{item}">
          {{ item.title }}
          <div style="font-size: 0.7rem">{{ item.inn }}</div>
        </template>
        <template v-slot:[`item.user`]="{item}">
          <v-icon v-if="item.user" color="success">mdi-check</v-icon>
          <v-icon v-else color="danger">mdi-close</v-icon>
        </template>

      </v-data-table>
    </v-col>

    <v-dialog v-model="editDialog" max-width="700">
      <CustomerEditor
          v-if="editDialog"
          @close="editDialog=false"
          v-model="editItem"
          @created="onCreated"
          @updated="onUpdated"
          :modal="true"
      />
    </v-dialog>
  </v-row>
</template>

<script>

import ResourceComponentHelper from "@/mixins/ResourceComponentHelper";
import CustomerEditor from "@/components/Customer/CustomerEditor";

export default {
  name: "CustomersView",
  components: {CustomerEditor},
  mixins: [ResourceComponentHelper],
  data() {
    return {
      headers: [
        {text: "ID", value: "id", sortable: true},
        {text: "Фамилия", value: "surname", sortable: true},
        {text: "Имя", value: "name", sortable: false},
        {text: "Отчество", value: "father_name", sortable: false},
        {text: "Телефон", value: "phone", sortable: false},
        {text: "E-mail", value: "email", sortable: false},
        {text: "Telegram", value: "telegram", sortable: false},
        {text: "Тип", value: "type", sortable: false},
       // {text: "Тип ЮЛ", value: "entity_type", sortable: false},
        {text: "Название", value: "title", sortable: false},
    //    {text: "ИНН", value: "inn", sortable: false},
        {text: "Баланс", value: "balance", sortable: false},
        {text: "Доступ", value: "user", sortable: false},
     //   {text: "Был в сети", value: "last_activity_date", sortable: false},
        {text: "", value: "actions", sortable: false},
      ],
      resourceKey: "customers",
      resourceApiRoute: `customers`,
    }
  },
}
</script>

<style scoped>

</style>