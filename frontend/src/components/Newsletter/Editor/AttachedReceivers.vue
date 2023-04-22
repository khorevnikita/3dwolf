<template>
  <div>
    Найдено: {{ totalCount }}
    <v-list>
      <v-list-item link v-for="customer in customers" :key="customer.id">
        <v-list-item-action @click="detach(customer)">
          <v-btn icon>
            <v-icon>mdi-chevron-left</v-icon>
          </v-btn>
        </v-list-item-action>
        <v-list-item-content>
          <v-list-item-title>{{customer.title}}</v-list-item-title>
          <v-list-item-subtitle>{{ customer.phone }} {{ customer.email }}</v-list-item-subtitle>
        </v-list-item-content>
      </v-list-item>
    </v-list>
    <v-pagination v-model="page" :length="pagesCount"/>
  </div>
</template>

<script>
import axios from "@/plugins/axios";

export default {
  name: "AttachedReceivers",
  props: ['newsletter', 'reFetch'],
  data() {
    return {
      customers: [],
      page: 1,
      pagesCount: 1,
      totalCount: 0
    }
  },
  created() {
    this.getCustomers();

    this.unsubscribe = this.$store.subscribe((mutation, state) => {
      if (mutation.type === 'attachToNewsletter') {
        if (state.attachedCustomer) {
          this.customers.unshift(state.attachedCustomer);
          this.$store.commit('attachToNewsletter', undefined);
          this.totalCount++;
        }
      }
    });
  },
  beforeDestroy() {
    this.unsubscribe;
  },
  watch: {
    page: {
      handler() {
        this.getCustomers();
      },
    },
    reFetch: {
      handler(v) {
        v ? this.getCustomers() : undefined;
      }
    }
  },
  methods: {
    getCustomers() {
      axios.get(`newsletters/${this.newsletter.id}/attached-receivers?page=${this.page}`).then(body => {
        this.customers = body.customers;
        this.totalCount = body.totalCount;
        this.pagesCount = body.pagesCount;
      })
    },
    detach(customer) {
      axios.post(`newsletters/${this.newsletter.id}/detach/${customer.id}`).then(() => {
        this.customers.splice(this.customers.indexOf(customer), 1);
        this.$store.commit('detachFromNewsletter', customer);
        this.totalCount--;
      })
    }
  }
}
</script>

<style scoped>

</style>