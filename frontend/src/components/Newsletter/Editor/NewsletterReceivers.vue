<template>
  <v-row class="mt-2">
    <v-col cols="6">
      <ReceiversFilter v-model="query"/>
      <v-btn class="mt-4" small color="secondary" @click="attachAll()">Выбрать всех</v-btn>
      <AvailableReceivers
          class="mt-4"
          :newsletter="newsletter"
          :query="query"
          :re-fetch="reFetch"
      />
    </v-col>
    <v-col cols="6">
      <v-btn small class="mt-11" color="secondary" @click="detachAll()">Убрать всех</v-btn>
      <AttachedReceivers
          class="mt-4"
          :newsletter="newsletter"
          :re-fetch="reFetch"
      />
    </v-col>
  </v-row>
</template>

<script>
import ReceiversFilter from "@/components/Newsletter/Editor/ReceiversFilter";
import AvailableReceivers from "@/components/Newsletter/Editor/AvailableReceivers";
import AttachedReceivers from "@/components/Newsletter/Editor/AttachedReceivers";
import axios from "@/plugins/axios";

export default {
  name: "NewsletterReceivers",
  components: {AttachedReceivers, AvailableReceivers, ReceiversFilter},
  props: ['newsletter'],
  data() {
    return {
      query: {},
      reFetch: false,
    }
  },
  methods: {
    attachAll() {
      axios.post(`newsletters/${this.newsletter.id}/attach-all`, {
        filter: this.query,
      }).then(() => {
        this.reFetchCustomers();
        this.$emit('customers_updated');
      })
    },
    detachAll() {
      axios.post(`newsletters/${this.newsletter.id}/detach-all`).then(() => {
        this.reFetchCustomers();
        this.$emit('customers_updated')
      })
    },
    reFetchCustomers() {
      this.reFetch = true;
      this.$nextTick(() => {
        this.reFetch = false;
      });
    },
  }
}
</script>

<style scoped>

</style>