<template>
  <v-row class="mx-3 mt-5">
    <v-col cols="12">
      <div class="d-flex align-items-center">
        <div class="text-h6">Задачи</div>
        <v-spacer/>
        <v-btn small @click="create()" color="primary">Создать</v-btn>
      </div>
    </v-col>
    <!--<v-col cols="12">
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
    </v-col>-->
    <v-col cols="12">
      <v-data-table
          :headers="headers"
          :items="items"
          :options.sync="options"
          :server-items-length="totalItems"
          :loading="loading"
          class="elevation-1 mt-3"
      >
        <template v-slot:[`item.date`]="{item}">
          {{ moment(item.date).format("DD.MM.YYYY") }}
        </template>
        <template v-slot:[`item.count`]="{item}">
          {{ item.completed_count }}/{{item.total_count}}
        </template>
        <template v-slot:[`item.actions`]="{item}">
          <v-btn small color="primary" :to="`/tasks/${item.date}`">Открыть список</v-btn>
        </template>
      </v-data-table>
    </v-col>

    <v-dialog v-model="editDialog" max-width="500">
      <TaskEditor
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
import TaskEditor from "@/components/Task/TaskEditor";

export default {
  name: "TasksView",
  components: {TaskEditor},
  mixins: [ResourceComponentHelper],
  data() {
    return {
      headers: [
        {text: "Дата", value: "date", sortable: false},
        {text: "Кол-во задач", value: "count", sortable: false},
        {text: "", value: "actions", sortable: false},
      ],
      resourceApiRoute: `tasks-schedule`,
      resourceKey: "tasksSchedule",
    }
  },
}
</script>

<style scoped>

</style>