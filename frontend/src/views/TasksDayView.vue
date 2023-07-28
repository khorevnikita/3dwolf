<template>
  <v-row class="mx-3 mt-5">
    <v-breadcrumbs :items="breadcrumps" divider="-"/>
    <v-col cols="12">
      <div class="d-flex align-items-center">
        <div class="text-h6">Задачи</div>
        <v-spacer/>
        <v-btn small @click="notifyAll()" color="primary" :disabled="loading" class="mr-3">Разослать уведомления</v-btn>
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
        <template v-slot:[`item.time`]="{item}">
          {{ moment(item.datetime).format("HH:mm") }}
        </template>
        <template v-slot:[`item.user`]="{item}">
          {{ item.user ? [item.user.name, item.user.surname].join(" ") : '-' }}
        </template>
        <template v-slot:[`item.status`]="{item}">
          <div class="d-flex">
            <v-tooltip top>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                    class="mr-2"
                    @click="notify(item)"
                    v-bind="attrs"
                    v-on="on"
                    text
                    x-small
                >
                  <v-icon :color="item.notified?'success':''">mdi-send-variant-clock-outline</v-icon>
                </v-btn>
              </template>
              <span>{{ item.notified ? 'Повторить уведомление' : 'Отправить уведомление' }}</span>
            </v-tooltip>

            <v-tooltip top>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                    @click="toggleComplete(item)"
                    v-bind="attrs"
                    v-on="on"
                    text
                    x-small
                >
                  <v-icon color="success" v-if="item.completed">mdi-check-circle</v-icon>
                  <v-icon color="" v-else>mdi-check</v-icon>&nbsp;
                  {{ item.completed ? 'Выполнено' : 'Не выполнено' }}
                </v-btn>
              </template>
              <span>{{ item.completed ? 'Отметить, как не выполнено' : 'Отметить, как выполнено' }}</span>
            </v-tooltip>

          </div>
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
import axios from "@/plugins/axios";
import Swal from "sweetalert2-khonik";

export default {
  name: "TasksView",
  components: {TaskEditor},
  mixins: [ResourceComponentHelper],
  data() {
    return {
      headers: [
        {text: "Время", value: "time", sortable: false},
        {text: "Название", value: "name", sortable: false},
        {text: "Описание", value: "description", sortable: false},
        {text: "Ответственный", value: "user", sortable: false},
        {text: "Статус", value: "status", sortable: false},
        {text: "", value: "actions", sortable: false},
      ],
      resourceKey: "tasks",
      resourceApiRoute: `tasks`,
      resourceApiParams: `date=${this.$route.params.date}`,
      vehicleGroups: [],
      deleteSwalTitle: `Безвозвратно удалить задачу?`,
      loading: false,
    }
  },
  computed: {
    breadcrumps() {
      return [
        {
          text: 'Задачи',
          disabled: false,
          href: '/tasks',
        },
        {
          text: `${moment(this.$route.params.date).format("DD.MM.YYYY")}`,
          disabled: true,
          href: '#',
        },
      ]
    }
  },
  methods: {
    async toggleComplete(task) {
      if (!task.completed) {
        const {isConfirmed, isDenied} = await Swal.fire({
          title: "Вы уверены, что задача выполнена?",
          showDenyButton: true,
          denyButtonText: "Перенести на другую дату",
          denyButtonColor: "#6e7881",

          showCancelButton: true,
          cancelButtonText: 'Отменить',

          showCloseButton: false,

          showConfirmButton: true,
          confirmButtonText: 'Подтверждаю',
        });
        if (isDenied) {
          // show edit dialog;
          this.edit(task);
          return;
        }
        if (!isConfirmed) {
          return;
        }
      }
      axios.post(`tasks/${task.id}/complete`, {
        completed: !task.completed,
      }).then(() => {
        task.completed = !task.completed;
      })
    },
    notifyAll() {
      this.loading = true;
      axios.post(`tasks/notify`, {
        date: this.$route.params.date
      }).then(() => {
        this.items.forEach((item) => item.notified = true);
        this.loading = false;
      })
    },
    notify(task) {
      axios.post(`tasks/${task.id}/notify`,).then(() => {
        task.notified = true;
      })
    }
  }
}
</script>

<style scoped>

</style>