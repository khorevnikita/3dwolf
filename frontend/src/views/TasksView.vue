<template>
  <v-row class="mx-3 mt-5">
    <v-col cols="12">
      <div class="d-flex align-items-center">
        <div class="text-h6">Задачи</div>
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
        <template v-slot:[`item.date`]="{item}">
          {{ moment(item.date).format("DD.MM.YYYY") }}
          <v-icon v-if="isToday(item.date)" small color="primary">mdi-circle</v-icon>
        </template>
        <template v-slot:[`item.count`]="{item}">
          {{ item.completed_count }}/{{ item.total_count }}
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
import moment from "moment";

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
  methods: {
    isToday(date) {
      return moment(date).format("YYYY-MM-DD") === moment().format("YYYY-MM-DD")
    }
  }
}
</script>

<style scoped>
/*.today {
  border:1px solid green;;
}*/
</style>