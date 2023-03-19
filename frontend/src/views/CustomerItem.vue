<template>
  <v-row>
    <v-col cols="12" md="9">
      <CustomerEditor v-if="model" v-model="model"/>
    </v-col>
    <v-col cols="12" md="3">
      <v-card>
        <v-card-title>Наряд-заказы</v-card-title>
        <v-card-text>
          <v-list>
            <v-list-item>
              <v-list-item-content>
                Заказ 1
              </v-list-item-content>
            </v-list-item>
            <v-list-item>
              <v-list-item-content>
                Заказ 2
              </v-list-item-content>
            </v-list-item>
          </v-list>
        </v-card-text>
        <v-card-actions>
          <v-btn small color="primary">Создать</v-btn>
        </v-card-actions>
      </v-card>
      <v-card class="mt-5">
        <v-card-subtitle>Оплачено</v-card-subtitle>
        <v-card-title>Р. 150 000</v-card-title>
        <v-card-actions>
          <v-btn small color="primary">Добавить поступление денег</v-btn>
        </v-card-actions>
      </v-card>
    </v-col>
    <v-col cols="12">
      <v-btn small color="error" @click="destroy()">Удалить</v-btn>
    </v-col>
  </v-row>
</template>

<script>
import axios from "@/plugins/axios";
import CustomerEditor from "@/components/Customer/CustomerEditor";
import Swal from "sweetalert2-khonik";

export default {
  name: "CustomerItem",
  components: {CustomerEditor},
  data() {
    return {
      id: this.$route.params.id,
      model: undefined
    }
  },
  created() {
    this.getModel();
  },
  methods: {
    getModel() {
      axios.get(`customers/${this.id}`).then(body => {
        this.model = body.customer;
      });
    },
    destroy(){
      Swal.fire({
        title: "Вы действительно хотите удалить клиента?",
        showDenyButton: true,
        denyButtonText: `Удалить`,
        showCancelButton: true,
        cancelButtonText: 'Отменить',
        showCloseButton: false,
        showConfirmButton: false,
      }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isDenied) {
          axios.delete(`customers/${this.id}`).then(() => {
            this.$router.replace('/customers');
          })
        }
      })
    }
  }
}
</script>

<style scoped>

</style>