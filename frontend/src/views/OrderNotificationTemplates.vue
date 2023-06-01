<template>
  <v-row class="mx-3 mt-5">
    <v-col cols="12">
      <div class="d-flex align-items-center">
        <div class="text-h6">Шаблоны уведомлений</div>
        <v-spacer/>
        <v-btn v-if="freeStatuses.length>0" small @click="create()" color="primary">Создать</v-btn>
      </div>
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
        <template v-slot:[`item.order_status`]="{item}">
          {{ statusLabel(item.order_status) }}
        </template>
        <template v-slot:[`item.template`]="{item}">
          <div v-html="item.template"></div>
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

    <v-dialog v-model="editDialog" max-width="700">
      <OrderNotificationTemplateEditDialog
          v-if="editDialog"
          @close="editDialog=false"
          v-model="editItem"
          @created="onCreated"
          @updated="onUpdated"
          :free-statuses="freeStatuses"
      />
    </v-dialog>
  </v-row>
</template>

<script>
import ResourceComponentHelper from "@/mixins/ResourceComponentHelper";
import OrderNotificationTemplateEditDialog
  from "@/components/OrderNotificationTemplate/OrderNotificationTemplateEditDialog";

export default {
  name: "OrderNotificationTemplates",
  components: {OrderNotificationTemplateEditDialog},
  mixins: [ResourceComponentHelper],
  data() {
    return {
      headers: [
        {text: "ID", value: "id", sortable: false},
        {text: "Статус", value: "order_status", sortable: false},
        //  {text: "Шаблон", value: "template", sortable: false},
        {text: "", value: "actions", sortable: false},
      ],
      resourceKey: "templates",
      resourceApiRoute: `order-notification-templates`,
      deleteSwalTitle: `Безвозвратно удалить шаблон?`,
      statuses: [
        {value: 'new', text: 'Новый'},
        {value: 'printing', text: 'В печати'},
        {value: 'moving', text: 'Перемещение на ПВЗ'},
        {value: 'moving_tk', text: 'Перемещение ТК'},
        {value: 'shipping', text: 'Готов к отгрузке'},
        {value: 'completed', text: 'Отгружен'},
        {value: 'canceled', text: 'Отменён'},
      ],
    }
  },
  computed: {
    busyStatuses() {
      if (!this.items) return [];
      return this.items.map(i => i.order_status);
    },
    freeStatuses() {
      return this.statuses.filter(x => !this.busyStatuses.includes(x.value));
    }
  },
  methods: {
    statusLabel(key) {
      return this.statuses.find(x => x.value === key)?.text;
    }
  }
}
</script>

<style scoped>

</style>