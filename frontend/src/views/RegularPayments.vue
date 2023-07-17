<template>
  <v-row class="mx-3 mt-5">
    <v-col cols="12">
      <div class="d-flex align-items-center">
        <div class="text-h6">Регулярные платежи</div>
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
              />
            </v-col>
            <v-col cols="12" md="4">
              <UserPicker
                  lang="Сотрудник"
                  v-model="query.user_id"
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
        <template v-slot:[`item.next_date`]="{item}">
          {{ item.next_date ? moment(item.next_date).format("DD.MM.YYYY") : '-' }}
        </template>
        <template v-slot:[`item.amount`]="{item}">
          {{ item.amount ? formatPrice(item.amount) : '-' }}
        </template>

        <template v-slot:[`item.user`]="{item}">
          {{ item.user ? [item.user.name, item.user.surname].join(" ") : '' }}
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
      <div class="mt-2 text-subtitle-1">Итого: <b>{{ formatPrice(totalSum) }}</b></div>
    </v-col>

    <v-dialog v-model="editDialog" max-width="700">
      <RegularPaymentEditor
          v-if="editDialog"
          @close="editDialog=false"
          v-model="editItem"
          @created="onCreated"
          @updated="onCreated"
      />
    </v-dialog>
  </v-row>
</template>

<script>
import ResourceComponentHelper from "@/mixins/ResourceComponentHelper";
import RegularPaymentEditor from "@/components/RegularPayment/RegularPaymentEditor";
import UserPicker from "@/components/Forms/UserPicker";
import axios from "@/plugins/axios";

export default {
  name: "RegularPayments",
  components: {UserPicker, RegularPaymentEditor},
  mixins: [ResourceComponentHelper],
  data() {
    return {
      headers: [
        {text: "ID", value: "id", sortable: true},
        {text: "Дата", value: "next_date", sortable: true},
        {text: "Кому", value: "recipient", sortable: false},
        {text: "Сотрудник", value: "user", sortable: false},
        {text: "Сумма", value: "amount", sortable: false},
        {text: "Описание", value: "description", sortable: false},
        {text: "", value: "actions", sortable: false},
      ],
      resourceKey: "regularPayments",
      resourceApiRoute: `regular-payments`,
      totalSum: 0,
    }
  },
  methods: {
    getItems() {
      axios.get(`${this.resourceApiRoute}?${this.resourceApiParams}&${this.setQueryString(this.query)}`).then(body => {
        this.items = body[this.resourceKey];
        this.totalItems = body.totalCount;
        this.pagesCount = body.pagesCount;
        this.totalSum = body.totalSum;
        this.loading = false;
      })
    },
  }
}
</script>

<style scoped>

</style>