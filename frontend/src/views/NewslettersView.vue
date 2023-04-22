<template>
  <v-row class="mx-3 mt-5">
    <v-col cols="12">
      <div class="d-flex align-items-center">
        <div class="text-h6">Email рассылки</div>
        <v-spacer/>
        <v-btn small to="/newsletters/create" color="primary">Создать</v-btn>
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
        <template v-slot:[`item.id`]="{item}">
          <v-btn text :to="`/newsletters/${item.id}`">
            {{ item.id }}
          </v-btn>
        </template>

        <template v-slot:[`item.progress`]="{item}">
          <v-progress-linear :value="item.progress"/>
        </template>

        <template v-slot:[`item.status`]="{item}">
          {{ item.status }}
        </template>

        <template v-slot:[`item.actions`]="{item}">
          <v-btn icon color="success" @click="send(item)" :disabled="!item.editable">
            <v-icon>mdi-play</v-icon>
          </v-btn>
          <v-btn :to="`/newsletters/${item.id}/edit`" icon color="warning" :disabled="!item.editable">
            <v-icon>mdi-pencil</v-icon>
          </v-btn>
          <v-btn icon color="error" class="ml-2" @click="destroy(item)" :disabled="!item.editable">
            <v-icon>mdi-delete</v-icon>
          </v-btn>
        </template>
      </v-data-table>
    </v-col>

    <!--<v-dialog v-model="editDialog"
              fullscreen
              hide-overlay
              persistent
              transition="dialog-bottom-transition">
      <NewsletterEditDialog
          v-if="editDialog"
          @close="editDialog=false"
          v-model="editItem"
          @created="onCreated"
          @updated="onUpdated"
          @customers_updated="onCustomersUpdated"
      />
    </v-dialog>-->
  </v-row>
</template>

<script>
import ResourceComponentHelper from "@/mixins/ResourceComponentHelper";
import Swal from "sweetalert2-khonik";
//import NewsletterEditDialog from "@/components/Newsletter/NewsletterEditDialog";
import axios from "@/plugins/axios";

export default {
  name: "NewslettersView",
  //components: {NewsletterEditDialog},
  mixins: [ResourceComponentHelper],
  data() {
    return {
      headers: [
        {text: "ID", value: "id", sortable: true},
        {text: "Название", value: "subject", sortable: true},
        {text: "Кол-во получателей", value: "customers_count", sortable: true},
        {text: "Прогресс", value: "progress", sortable: true},
        {text: "Статус", value: "status", sortable: true},
        {text: "", value: "actions", sortable: false},
      ],
      resourceKey: "newsletters",
      resourceApiRoute: `newsletters`,
      deleteSwalTitle: "Вы действительно хотите удалить рассылку?",
    }
  },
  methods: {
    onCreated(resource) {
      this.$set(this, 'items', [
        resource,
        ...this.items,
      ])
    },
    /*onCustomersUpdated() {
      this.getItems()
    },*/
    send(item) {
      Swal.fire({
        title: "Подтверждаете отправку рассылки?",
        showDenyButton: false,
        showCancelButton: true,
        cancelButtonText: 'Отменить',
        showCloseButton: false,
        showConfirmButton: true,
        confirmButtonText: 'Подтверждаю'
      }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
          axios.post(`newsletters/${item.id}/send`).then(() => {
            item.status = 'sending';
            item.editable = false;
          })
        }
      });
    },
  }
}
</script>

<style scoped>

</style>