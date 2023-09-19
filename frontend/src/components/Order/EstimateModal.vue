<template>
  <v-card>
    <v-card-title>Выберите смету</v-card-title>
    <v-card-text>
      <EstimatePicker
          label="Смета"
          v-model="estimateId"
          :error="errors.estimate_id"
      />
    </v-card-text>
    <v-card-actions>
      <v-btn text @click="$emit('close')">Закрыть</v-btn>
      <v-spacer/>
      <v-btn color="primary" @click="save()">Сохранить</v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
import axios from "@/plugins/axios";
import EstimatePicker from "@/components/Forms/EstimatePicker";

export default {
  name: "EstimateModal",
  components: {EstimatePicker},
  props: ['orderId'],
  data() {
    return {
      estimateId: null,
      errors: {}
    }
  },
  methods: {
    save() {
      axios.post(`orders/${this.orderId}/fill`, {
        estimate_id: this.estimateId
      }).then(() => {
        this.$emit('selected')
        this.$emit('close')
      }).catch(err => {
        this.errors = err.body.errors;
      })
    }
  }
}
</script>

<style scoped>

</style>