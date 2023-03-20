<template>
  <v-row>
    <v-col cols="12" md="9">
      <CustomerEditor v-if="model" v-model="model"/>
    </v-col>
    <v-col cols="12" md="3">
      <CustomerOrder :customer-id="model.id"/>
      <CustomerPayment class="mt-5" :customer="model"/>
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
import CustomerOrder from "@/components/Customer/CustomerOrder";
import CustomerPayment from "@/components/Customer/CustomerPayment";

export default {
  name: "CustomerItem",
  components: {CustomerPayment, CustomerOrder, CustomerEditor},
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