<template>
  <v-card>
    <v-card-title>Пользователь</v-card-title>
    <v-card-text>
      <v-list>
        <v-list-item v-for="user in items">
          <v-list-item-content>
            <v-list-item-title>{{ user.name }} {{ user.surname }}</v-list-item-title>
            <v-list-item-subtitle>{{ user.email }}</v-list-item-subtitle>
          </v-list-item-content>
          <v-list-item-action>
            <v-btn color="warning" icon @click="edit(user)">
              <v-icon>mdi-pencil</v-icon>
            </v-btn>
            <v-btn
                color="primary"
                icon
                @click="resetUser(user)"
            >
              <v-icon>mdi-refresh</v-icon>
            </v-btn>
            <v-btn color="error" icon @click="destroy(user)">
              <v-icon>mdi-delete</v-icon>
            </v-btn>
          </v-list-item-action>
        </v-list-item>
      </v-list>

      <v-dialog v-model="editDialog" max-width="500">
        <CreateUserDialog
            :customer-id="customerId"
            v-if="editDialog"
            @close="editDialog=false"
            v-model="editItem"
            @created="onCreated"
            @updated="onUpdated"
        />
      </v-dialog>

    </v-card-text>
    <v-card-actions>
      <!--<v-btn
          v-if="items.length"
          @click="create()"
          color="primary"
      >Выдать доступ
      </v-btn>-->
      <v-btn
          v-if="items.length===0"
          color="primary"
          @click="createUser()"
      >Выдать доступ
      </v-btn>

    </v-card-actions>
  </v-card>

</template>

<script>
import ResourceComponentHelper from "@/mixins/ResourceComponentHelper";
import CreateUserDialog from "@/components/Customer/CreateUserDialog";
import axios from "@/plugins/axios";
import Swal from "sweetalert2-khonik";

export default {
  name: "CustomerUsers",
  components: {CreateUserDialog},
  mixins: [ResourceComponentHelper],
  props: ['customerId'],
  data() {
    return {
      headers: [
        {text: "ID", value: "id", sortable: false},
        {text: "Фамилия", value: "surname", sortable: false},
        {text: "Имя", value: "name", sortable: false},
        {text: "E-mail", value: "email", sortable: false},
        {text: "", value: "actions", sortable: false},
      ],
      resourceKey: "users",
      resourceApiRoute: `users`,
      resourceApiParams: `customer_id=${this.customerId}`,
      deleteSwalTitle: `Безвозвратно удалить пользователя?`,
      editDialog: false,
    }
  },
  methods: {
    createUser() {
      axios.post(`customers/${this.customerId}/user`).then(({user}) => {
        this.onCreated(user);
      })
    },
    resetUser(user) {
      axios.post(`users/${user.id}/reset`).then(() => {
        Swal.fire("Пароль сброшен и отправлен на почту")
      })
    }
  }
}
</script>

<style scoped>

</style>