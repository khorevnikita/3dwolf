<template>
  <v-row class="mx-3 mt-5">
    <v-col cols="12">
      <div class="d-flex align-items-center">
        <div class="text-h6">Договора</div>
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
              <CustomerPicker
                  v-model="query.customer_id"
                  :dense="true"
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
        <template v-slot:[`item.status`]="{item}">
          {{ item.status === 'new' ? 'Новый' : item.status === 'process' ? 'В работе' : 'Завершен' }}
        </template>
        <template v-slot:[`item.date`]="{item}">
          {{ item.date ? moment(item.date).format("DD.MM.YYYY") : '-' }}
        </template>
        <template v-slot:[`item.customer_id`]="{item}">
          {{ item.customer ? item.customer.title : '-' }}
        </template>
        <template v-slot:[`item.amount`]="{item}">
          {{ formatPrice(item.amount) }}
        </template>
        <template v-slot:[`item.actions`]="{item}">
          <v-btn color="info" icon @click="download(item)">
            <v-icon>mdi-file-pdf-box</v-icon>
          </v-btn>
          <v-btn color="warning" icon @click="edit(item)">
            <v-icon>mdi-pencil</v-icon>
          </v-btn>
          <v-btn color="error" icon @click="destroy(item)">
            <v-icon>mdi-delete</v-icon>
          </v-btn>
        </template>
      </v-data-table>
    </v-col>

    <v-dialog v-model="editDialog" max-width="700">
      <ContractEditor
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
import ContractEditor from "@/components/Contract/ContractEditor";
import CustomerPicker from "@/components/Forms/CustomerPicker";
import axios from "@/plugins/axios";

export default {
  name: "ContractsView",
  components: {CustomerPicker, ContractEditor},
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
      deleteSwalTitle: `Безвозвратно удалить договор?`,
    }
  },
  methods: {
    download(item) {
      axios.get(`contracts/${item.id}/export-auth?type=pdf`).then(body => {
        window.open(body.download_link, '_blank')
      })
    }
  }
}
</script>

<style scoped>

</style>