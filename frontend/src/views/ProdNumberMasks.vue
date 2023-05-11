<template>
  <v-row class="mx-3 mt-5">
    <v-col cols="12">
      <div class="d-flex align-items-center">
        <div class="text-h6">Маски для внутренних номеров</div>
        <v-spacer/>
        <v-btn small @click="create()" color="primary">Создать</v-btn>
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
      <ProdNumberMaskEditor
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
import ProdNumberMaskEditor from "@/components/ProdNumberMask/ProdNumberMaskEditor";

export default {
  name: "ProdNumberMasksView",
  components: {ProdNumberMaskEditor},
  mixins: [ResourceComponentHelper],
  data() {
    return {
      headers: [
        {text: "ID", value: "id", sortable: false},
        {text: "Произв. номер", value: "prod_number", sortable: false},
        {text: "Маска внутр. номера", value: "mask", sortable: false},
        {text: "", value: "actions", sortable: false},
      ],
      resourceKey: "prodNumberMasks",
      resourceApiRoute: `prod-number-masks`,
      deleteSwalTitle: `Безвозвратно удалить маску?`,
    }
  },
}
</script>

<style scoped>

</style>