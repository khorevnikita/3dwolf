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
      <v-simple-table>
        <template v-slot:default>
          <thead>
          <tr>
            <th class="text-left">
              Дата
            </th>
            <th class="text-left">
              Задачи
            </th>
            <th class="text-left">
            </th>
          </tr>
          </thead>
          <tbody>

          <tr v-for="(item,i) in items" :key="i">
            <td> {{ moment(item.date).format("DD.MM.YYYY") }}
            <v-icon v-if="isToday(item.date)" small color="primary">mdi-circle</v-icon>
            </td>
            <td>{{ item.completed_count }}/{{ item.total_count }}</td>
            <td>
              <v-btn small color="primary" :to="`/tasks/${item.date}`">Открыть список</v-btn>
            </td>
          </tr>
          </tbody>
        </template>
      </v-simple-table>
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